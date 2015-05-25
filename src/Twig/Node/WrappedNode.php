<?php

namespace TPro\Acl\Twig\Node;

class WrappedNode extends NodeDecorator
{
    public function compile(\Twig_Compiler $compiler)
    {
        var_dump('before');
        parent::compile($compiler);
        var_dump('after');
    }
}