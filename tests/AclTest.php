<?php

class AclTest extends \PHPUnit_Framework_TestCase
{
    const PRIVILEGE_READ = 'r';
    const PRIVILEGE_WRITE = 'w';
    const PRIVILEGE_NONE = 'n';

    public function testIsRouteAllowed()
    {
        $acl = new \TPro\Acl\Acl();

        $pattern = '/';
        $acl->setRouteResource($pattern, self::PRIVILEGE_READ);

        $this->assertTrue($this->isRouteAllowed($acl, $pattern, self::PRIVILEGE_READ));
        $this->assertTrue($this->isRouteAllowed($acl, $pattern, [self::PRIVILEGE_READ]));
        $this->assertTrue($this->isRouteAllowed($acl, $pattern, [self::PRIVILEGE_READ, self::PRIVILEGE_WRITE]));
        $this->assertTrue($this->isRouteAllowed($acl, $pattern));

        $this->assertFalse($this->isRouteAllowed($acl, $pattern, self::PRIVILEGE_WRITE));
    }

    private function isRouteAllowed(\TPro\Acl\Acl $acl, $pattern, $privilege = null)
    {
        /* isAllowed is alias of isRouteAllowed to keep compatibility with Zend AclInterface */
        return $acl->isRouteAllowed($pattern, $privilege) && $acl->isAllowed(null, $pattern, $privilege);
    }

    public function testIsBlockAllowed()
    {
        $acl = new \TPro\Acl\Acl();

        $block_name = 'block_name';

        $acl->addBlockResource($block_name, self::PRIVILEGE_READ);
        $this->assertTrue($acl->isBlockAllowed($block_name, self::PRIVILEGE_READ));
        $this->assertTrue($acl->isBlockAllowed($block_name, [self::PRIVILEGE_READ, self::PRIVILEGE_WRITE]));
        $this->assertFalse($acl->isBlockAllowed($block_name, self::PRIVILEGE_WRITE));
        $this->assertFalse($acl->isBlockAllowed($block_name, [self::PRIVILEGE_WRITE]));
    }

    public function testAccessInheritance()
    {
        $acl = new \TPro\Acl\Acl();

        $pattern = '/';
        $acl->setRouteResource($pattern, self::PRIVILEGE_READ);

        /* If no block permissions specified (block might be unregistered)
         * then route permissions must be used */
        $block_name = 'block_name';
        $this->assertTrue($acl->isBlockAllowed($block_name, self::PRIVILEGE_READ));
        $this->assertFalse($acl->isBlockAllowed($block_name, self::PRIVILEGE_WRITE));
    }
}
