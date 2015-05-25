<?php

namespace TPro\Acl\Twig\TokenWrapper\Decorator;

use TPro\Acl\Twig\TokenWrapper\Wrapper\TokenWrapper;

class WrapperDecorator extends TokenWrapper
{
    public function __construct(TokenWrapper $tokenWrapper)
    {
        $this->tokenWrapper = $tokenWrapper;
    }

    public function beforeCompile()
    {
        return $this->tokenWrapper->beforeCompile();
    }

    public function afterCompile()
    {
        return $this->tokenWrapper->afterCompile();
    }
}