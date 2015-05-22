<?php

namespace TPro\Acl\Twig\NodeWrapper;

use TPro\Acl\Twig\NodeWrapper\WrapperData\WrapperData;
use Twig_Compiler;

abstract class TokenWrapper
{
    /** @var  Twig_Compiler */
    protected $compiler;

    abstract public function beforeCompile(WrapperData $data);

    abstract public function afterCompile(WrapperData $data);

    public function condition()
    {
        return true;
    }

    public function setCompiler(Twig_Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
}
