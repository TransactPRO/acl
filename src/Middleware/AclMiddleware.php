<?php

namespace TPro\Acl\Middleware;

use PDO;
use Slim\Middleware;
use TPro\Acl\Acl;
use Zend\Authentication\AuthenticationService;

/**
 * Class AclMiddleware
 *
 * Middleware resolves logged user roles and fetches permissions for current route and its blocks
 *
 * @package PAK\Classes\Acl
 */
class AclMiddleware extends Middleware
{
    /* Hook must be executed before Authentication hook,
     * so this must be set to something less than 10 */
    const HOOK_PRIORITY = 5;

    const ROLES_SEPARATOR = ',';

    /** @var Acl */
    private $acl;

    /** @var PDO */
    private $pdo;

    private $settings = [
        'guestRoleId' => 1
    ];

    public function __construct(PDO $pdo, Acl $acl, array $settings = [])
    {
        $this->pdo = $pdo;
        $this->acl = $acl;
        $this->applySettings($settings);
    }

    /**
     * @param array $settings
     */
    protected function applySettings(array $settings = [])
    {
        if (empty($settings)) return;

        foreach ($settings as $setting => $value) {
            if (!isset($this->settings[$setting])) {
                throw new \InvalidArgumentException("Trying to set non-existing setting ($setting)");
            }

            $this->settings[$setting] = $value;
        }
    }

    public function call()
    {
        $this->app->hook('slim.before.dispatch', $this->getHookClosure(), self::HOOK_PRIORITY);
        $this->next->call();
    }

    /**
     * Returns closure that sets user route and block permissions
     *
     * @return callable
     */
    protected function getHookClosure()
    {
        $router = $this->app->router;

        /** @var AuthenticationService $auth */
        /* jeremykendall/slim-auth must be bootstrapped at this point! */
        $auth = $this->app->auth;

        return function () use ($router, $auth) {
            /** @var AuthenticationService $auth */
            // Set role ID to default (guest) if user isn't authorized, otherwise get user role ID
            $identity = $auth->getIdentity();
            if (is_null($identity['role'])) {
                $role_id = $this->settings['guestRoleId'];
            } else {
                $role_id = $auth->getIdentity()['role'];
            }

            // Fetch route permissions for current user
            $route_pattern = $router->getCurrentRoute()->getPattern();
            $route_access = $this->fetchRouteAccess($route_pattern, $role_id);

            // Allow route
            $privilege = $route_access['privilege'] ?: null;
            $this->acl->setRouteResource($route_access['route_pattern'], $privilege);

            // Fetch block permissions
            $blocks_access = $this->fetchBlocksAccess($route_pattern, $role_id);

            // Allow blocks
            foreach ($blocks_access as $access) {
                $this->acl->addBlockResource($access['block_name'], $access['privilege']);
            }
        };
    }

    /**
     * Fetches appropriate route permission for set of roles
     *
     * @param string $route_pattern
     * @param int $role_id
     * @return array|bool
     */
    protected function fetchRouteAccess($route_pattern, $role_id)
    {
        $sql = "SELECT route.route_id, route.pattern AS route_pattern, rra.privilege AS privilege
                FROM role
                INNER JOIN role_route_access rra ON role.id = rra.role_id
                INNER JOIN route ON route.route_id = rra.route_id
                WHERE role.id = :role_id AND route.pattern = :route_pattern";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':route_pattern' => $route_pattern,
            ':role_id' => $role_id,
        ]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches appropriate block permissions for set of roles
     *
     * @param string $route_pattern
     * @param int $role_id
     * @return array
     */
    protected function fetchBlocksAccess($route_pattern, $role_id)
    {
        $sql = "SELECT rba.block_name, rba.privilege
                FROM role
                INNER JOIN role_block_access rba ON role.id = rba.role_id
                INNER JOIN route ON route.route_id = rba.route_id
                WHERE route.pattern = :route_pattern AND role.id = :role_id";
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':route_pattern' => $route_pattern,
            ':role_id' => $role_id
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns query-ready list of roles
     *
     * Transforms string
     * "guest,admin,superuser"
     * into
     * "'guest','admin','superuser'"
     *
     * @deprecated According to latest requirements there will be only single role attached to user
     * @param $roles_string
     * @return string
     */
    protected function quoteRolesString($roles_string)
    {
        $roles = explode(self::ROLES_SEPARATOR, $roles_string);
        $quoted_roles_arr = $this->quoteArrayElements($roles);

        return implode(self::ROLES_SEPARATOR, $quoted_roles_arr);
    }

    /**
     * Applies PDO escape and quoting to each $arr element
     *
     * @param array $arr
     * @return string
     */
    protected function quoteArrayElements(array $arr)
    {
        return array_map(function ($value) {
            return $this->pdo->quote($value);
        }, $arr);
    }
}
