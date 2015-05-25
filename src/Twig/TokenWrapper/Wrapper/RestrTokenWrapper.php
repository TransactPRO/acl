<?php

namespace TPro\Acl\Twig\TokenWrapper\Wrapper;

use TPro\Acl\Twig\TokenWrapper\Data\RestrWrapperData;

abstract class RestrTokenWrapper extends TokenWrapper
{
    /**
     * @param RestrWrapperData $data
     */
    public function setData(RestrWrapperData $data)
    {
        parent::setData($data);
    }
}