<?php

namespace TPro\Acl\Twig\TokenWrapper\Decorator;

use TPro\Acl\Twig\TokenWrapper\Wrapper\TokenWrapper;
use Twig_Compiler;

/**
 * Class WrapperCompiler decorates TokenWrapper by compiling results of
 * beforeCompile and afterCompile methods into the Twig output
 *
 * @package TPro\Acl\Twig\TokenWrapper\Decorator\
 */
class WrapperCompiler extends WrapperDecorator
{
    /** @var Twig_Compiler */
    protected $compiler;

    /**
     * @param TokenWrapper $tokenWrapper Object to decorate
     * @param Twig_Compiler $compiler
     */
    public function __construct(TokenWrapper $tokenWrapper, Twig_Compiler $compiler)
    {
        parent::__construct($tokenWrapper);
        $this->compiler = $compiler;
    }

    /**
     * @return void
     */
    public function beforeCompile()
    {
        $this->compile(parent::beforeCompile());
    }

    /**
     * @return void
     */
    public function afterCompile()
    {
        $this->compile(parent::afterCompile());
    }

    /**
     * Displays text in compiled Twig template
     * @param $html
     */
    protected function compile($html)
    {
        $this->compiler->raw('echo "' . $html . '";');
    }
}