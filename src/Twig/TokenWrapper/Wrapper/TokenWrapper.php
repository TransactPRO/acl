<?php

namespace TPro\Acl\Twig\TokenWrapper\Wrapper;

use TPro\Acl\Twig\TokenWrapper\Data\WrapperData;
use Twig_Compiler;

abstract class TokenWrapper
{
    /** @var  Twig_Compiler */
    protected $compiler;

    /** @var  WrapperData */
    protected $data;

    /**
     * Action to execute before Twig token compilation
     * @return string|void
     */
    abstract public function beforeCompile();

    /**
     * Action to execute after Twig token compilation
     * @return string|void
     */
    abstract public function afterCompile();

    /**
     * @param mixed $data
     */
    public function setData(WrapperData $data)
    {
        $this->data = $data;
    }
}
