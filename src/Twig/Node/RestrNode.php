<?php

namespace TPro\Acl\Twig\Node;

use TPro\Acl\Twig\NodeWrapper\RestrTokenWrapper;
use TPro\Acl\Twig\NodeWrapper\WrapperData\WrapperData;
use Twig_Compiler;
use Twig_Node;

class RestrNode extends Twig_Node
{
    protected $preOutputClosure;
    protected $postOutputClosure;

    public function __construct(Twig_Node $body, RestrTokenWrapper $wrapper = null, WrapperData $data)
    {
        parent::__construct(array('body' => $body));

        $this->wrapper = $wrapper;
        $this->wrapperData = $data;
    }

    /**
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        if (isset($this->wrapper)) {
            $this->wrapper->setCompiler($compiler);
            $this->wrapper->beforeCompile($this->wrapperData);
        }

        $compiler->subcompile($this->getNode('body'));

        if (isset($this->wrapper)) {
            $this->wrapper->afterCompile($this->wrapperData);
        }

        return;
    }
}