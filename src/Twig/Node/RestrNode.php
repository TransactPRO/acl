<?php

namespace TPro\Acl\Twig\Node;

use Pak\Classes\AclRestrTokenWrapper;
use TPro\Acl\Twig\TokenWrapper\Decorator\WrapperCompiler;
use Twig_Compiler;
use Twig_Node;

class RestrNode extends Twig_Node
{
    /**
     * @param Twig_Node $body
     * @param AclRestrTokenWrapper $wrapper
     */
    public function __construct(Twig_Node $body, AclRestrTokenWrapper $wrapper = null)
    {
        parent::__construct(array('body' => $body));

        $this->wrapper = $wrapper;
    }

    /**
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        // Decorate manually if TokenWrapper is set
        if (isset($this->wrapper)) {
            $wrapperCompiler = new WrapperCompiler($this->wrapper, $compiler);
            $wrapperCompiler->beforeCompile();

            $compiler->subcompile($this->getNode('body'));

            $wrapperCompiler->afterCompile();
        } else {
            $compiler->subcompile($this->getNode('body'));
        }
    }
}
