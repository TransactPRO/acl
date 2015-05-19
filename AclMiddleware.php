<?php

namespace Pak\Classes\Acl;

use PDO;
use Slim\Middleware;
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

    public function __construct(PDO $pdo, Acl $acl)
    {
        $this->pdo = $pdo;
        $this->acl = $acl;
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
            // Get role if user is authorized or set to `guest` otherwise
            /** @var AuthenticationService $auth */
            $roles = $auth->getIdentity()['role'] ?: 'guest';

            // Fetch route permissions
            $route = $router->getCurrentRoute()->getPattern();
            $route_permission = $this->fetchRoutePermission($route, $roles);

            // Allow route
            $privilege = $route_permission['privilege'] ?: null;
            $this->acl->setRouteResource($route_permission['route_pattern'], $privilege);

            // Fetch block permissions
            $route_permission_id = $route_permission['id'];
            $block_permissions = $this->fetchBlockPermissions($route_permission_id, $roles);

            // Allow blocks
            foreach ($block_permissions as $permission) {
                $this->acl->addBlockResource($permission['block_name'], $permission['privilege']);
            }
        };
    }

    /**
     * Fetches appropriate route permission for set of roles
     *
     * Todo: check index for IN query
     * Todo: what if there are multiple permissions for user roles found?
     * @param $route
     * @param array $roles
     * @return bool|array
     */
    protected function fetchRoutePermission($route, $roles)
    {
        $roles_str = $this->quoteRolesString($roles);

        $query = $this->pdo->prepare("SELECT * FROM role_route_permission WHERE route_pattern = :route AND role IN ($roles_str)");
        $query->execute([
            ':route' => $route,
        ]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches appropriate block permissions for set of roles
     *
     * @param $route_permission_id
     * @param $roles
     * @return array
     */
    protected function fetchBlockPermissions($route_permission_id, $roles)
    {
        $roles_str = $this->quoteRolesString($roles);

        $query = $this->pdo->prepare("SELECT * FROM role_block_permission WHERE role_route_permission_id = :role_route_permission_id AND role IN ($roles_str)");
        $query->execute([':role_route_permission_id' => $route_permission_id]);

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
