<?php

namespace TPro\Acl\Twig\Node;

use Twig_Node;

class NoAccessNode extends AccessNodeBase
{
    public function __construct(Twig_Node $body)
    {
        parent::__construct(['body' => $body]);
    }
}
