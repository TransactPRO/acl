<?php

namespace TPro\Acl\Resource;

/**
 * Class ResourceAccess represents user access rights to resource (route or block)
 *
 * @package Pak\Classes\Acl\Resource
 */
class ResourceAccess
{
    /** @var string Resource name */
    protected $name;

    /** @var string Permitted actions */
    protected $privilege;

    /**
     * @param string $name
     * @param string $privilege
     */
    public function __construct($name, $privilege)
    {
        $this->setName($name);
        $this->setPrivilege($privilege);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param string $privilege
     */
    protected function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
    }

    /**
     * @param string $name
     */
    protected function setName($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("Resource name cannot be empty");
        }

        $this->name = $name;
    }
}
