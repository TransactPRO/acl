<?php

namespace TPro\Slim\Acl\Twig\Node;

use Twig_Compiler;
use Twig_Node;

class RestrNode extends Twig_Node
{
    public function __construct(Twig_Node $body)
    {
        parent::__construct(array('body' => $body));
    }

    /**
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('body'));

        return;
    }
}