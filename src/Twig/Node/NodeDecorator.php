<?php

namespace TPro\Acl\Twig\Node;

abstract class NodeDecorator extends RestrNode
{
    public function __construct(RestrNode $node)
    {
        $this->node = $node;
        return $node;
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $this->node->compile($compiler);
    }
}
