<?php

namespace TPro\Slim\Acl\Twig\Extension;

use TPro\Slim\Acl\Acl;
use TPro\Slim\Acl\Twig\TokenParser\AccessTokenParser;
use TPro\Slim\Acl\Twig\TokenParser\NoAccessTokenParser;
use TPro\Slim\Acl\Twig\TokenParser\RestrTokenParser;
use Twig_Extension;

class AclExtension extends Twig_Extension
{
    /**
     * @var array
     */
    protected $acl;

    /**
     * @param Acl $acl
     */
    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * Returns the token parser instances to add to the existing list.
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return [
            new RestrTokenParser($this->acl),
            new AccessTokenParser(),
            new NoAccessTokenParser()
        ];
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return 'permissions';
    }
}
