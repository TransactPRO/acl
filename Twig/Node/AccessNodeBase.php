<?php

namespace Pak\Classes\Acl\Twig\Node;

use Twig_Compiler;
use Twig_Node;

class AccessNodeBase extends Twig_Node
{
    /** @var bool Indicates whether node will be compiled */
    protected $enabled = false;

    /**
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        if (!$this->isEnabled()) return;

        $compiler->subcompile($this->getNode('body'));
        return;
    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}