<?php

namespace Pak\Classes\Acl\Twig\Node;

use Twig_Node;

class AccessNode extends AccessNodeBase
{
    public function __construct(Twig_Node $body, array $permissions)
    {
        parent::__construct(['body' => $body], ['permissions' => $permissions]);
    }
}