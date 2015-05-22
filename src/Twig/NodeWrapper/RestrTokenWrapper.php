<?php

namespace TPro\Acl\Twig\NodeWrapper;


use TPro\Acl\Twig\NodeWrapper\WrapperData\RestrWrapperData;
use TPro\Acl\Twig\NodeWrapper\WrapperData\WrapperData;

class RestrTokenWrapper extends TokenWrapper
{
    public function beforeCompile(WrapperData $data)
    {
        /** @var RestrWrapperData $data */
        $text = 'begin ' . $data->restrId;

        $this->compiler->raw('echo "' . $text . '";');
    }

    public function afterCompile(WrapperData $data)
    {
        /** @var RestrWrapperData $data */
        $text = 'end ' . $data->restrId;

        $this->compiler->raw('echo "' . $text . '";');
    }

    public function condition()
    {
        return true;
    }
}
