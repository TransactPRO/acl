<?php
namespace TPro\Acl;

use InvalidArgumentException;
use TPro\Acl\Resource\ResourceAccess;
use Zend\Permissions\Acl\AclInterface;

/**
 * Custom implementation of Zend Acl, although it has almost nothing to do with original.
 * Class implements AclInterface to stay compatible with jeremykendall/slim-auth.
 *
 * @package Pak\Classes\Acl
 */
class Acl implements AclInterface
{
    /** @var ResourceAccess|null */
    protected $routeResource = null;

    /** @var array */
    protected $blockResources = [];

    /**
     * Checks whether user has access to requested block.
     * If permission for block is not specified then route permission is used.
     *
     * @param string $block_name
     * @param string|array|null $privilege
     * @return bool
     */
    public function isBlockAllowed($block_name, $privilege = null)
    {
        /* Use block permission if exists */
        if ($this->hasBlockResource($block_name)) {
            /** @var ResourceAccess $blockAccess */
            $blockAccess = $this->blockResources[$block_name];

            if (is_string($privilege)) {
                return $blockAccess->getPrivilege() === $privilege;
            } else if (is_array($privilege)) {
                return in_array($blockAccess->getPrivilege(), $privilege);
            }
        }

        /* Otherwise use route permission */
        if (is_string($privilege)) {
            return $this->routeResource->getPrivilege() === $privilege;
        } else if (is_array($privilege)) {
            return in_array($this->routeResource->getPrivilege(), $privilege);
        }

        throw new InvalidArgumentException('Privilege must be string or array');
    }

    /**
     * Checks whether user has permission to route
     *
     * @param string $route_pattern
     * @param string|array|null $privileges
     * @return bool
     */
    public function isRouteAllowed($route_pattern, $privileges = null)
    {
        /* No route permission exists or requested route do not match */
        if (!$this->hasRouteResource() || $this->routeResource->getName() !== $route_pattern) {
            return false;
        }

        /* Allow route if no special privileges required */
        if (is_null($privileges)) {
            return true;
        }

        /* Verify privileges */
        $userRoutePrivilege = $this->routeResource->getPrivilege();
        switch (true) {
            case (is_string($privileges)):
                return $privileges === $userRoutePrivilege;
                break;
            case (is_array($privileges)):
                return in_array($userRoutePrivilege, $privileges);
                break;
        }

        throw new InvalidArgumentException('`privileges` must be string, array, or null');
    }

    /**
     * @param string $block_name
     * @param string $privilege
     */
    public function addBlockResource($block_name, $privilege)
    {
        // Check if block was set already
        if (isset($this->blockResources[$block_name])) {
            throw new InvalidArgumentException("'$block_name' has already been set");
        }

        $this->blockResources[$block_name] = new ResourceAccess($block_name, $privilege);
    }

    /**
     * @param $route_pattern
     * @param $privilege
     */
    public function setRouteResource($route_pattern, $privilege)
    {
        try {
            $this->routeResource = new ResourceAccess($route_pattern, $privilege);
        } catch (InvalidArgumentException $e) {

        }
    }

    /**
     * @return bool
     */
    public function hasRouteResource()
    {
        return !is_null($this->routeResource);
    }

    /**
     * @param $block_name
     * @return bool
     */
    public function hasBlockResource($block_name)
    {
        return isset($this->blockResources[$block_name]);
    }

    /**
     * @param string|\Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @return bool
     */
    public function hasResource($resource)
    {
        return $this->hasRouteResource($resource);
    }

    /**
     * @param null $roles
     * @param null $resource
     * @param null $privilege
     * @return bool
     */
    public function isAllowed($roles = null, $resource = null, $privilege = null)
    {
        return $this->isRouteAllowed($resource);
    }
}