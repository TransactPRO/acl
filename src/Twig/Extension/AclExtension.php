<?php

namespace TPro\Acl\Twig\Extension;

use TPro\Acl\Acl;
use TPro\Acl\Twig\NodeWrapper\RestrTokenWrapper;
use TPro\Acl\Twig\NodeWrapper\TokenWrapper;
use TPro\Acl\Twig\TokenParser\AccessTokenParser;
use TPro\Acl\Twig\TokenParser\NoAccessTokenParser;
use TPro\Acl\Twig\TokenParser\RestrTokenParser;
use Twig_Extension;

class AclExtension extends Twig_Extension
{
    /** @var array */
    protected $acl;

    /** @var RestrTokenWrapper  */
    protected $restrTokenWrapper;

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
            new RestrTokenParser($this->acl, $this->getRestrTokenWrapper()),
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

    /**
     * @param RestrTokenWrapper $restrTokenWrapper
     */
    public function setRestrTokenWrapper(RestrTokenWrapper $restrTokenWrapper)
    {
        $this->restrTokenWrapper = $restrTokenWrapper;
    }

    /**
     * @return RestrTokenWrapper
     */
    protected function getRestrTokenWrapper()
    {
        return $this->restrTokenWrapper;
    }
}
