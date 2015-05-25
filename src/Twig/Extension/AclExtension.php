<?php

namespace TPro\Acl\Twig\Extension;

use Pak\Classes\AclRestrTokenWrapper;
use TPro\Acl\Acl;
use TPro\Acl\Twig\TokenParser\AccessTokenParser;
use TPro\Acl\Twig\TokenParser\NoAccessTokenParser;
use TPro\Acl\Twig\TokenParser\RestrTokenParser;
use Twig_Extension;

class AclExtension extends Twig_Extension
{
    /** @var array */
    protected $acl;

    /** @var AclRestrTokenWrapper */
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
     * @param AclRestrTokenWrapper $restrTokenWrapper
     */
    public function setRestrTokenWrapper(AclRestrTokenWrapper $restrTokenWrapper)
    {
        $this->restrTokenWrapper = $restrTokenWrapper;
    }

    /**
     * @return AclRestrTokenWrapper
     */
    protected function getRestrTokenWrapper()
    {
        return $this->restrTokenWrapper;
    }
}
