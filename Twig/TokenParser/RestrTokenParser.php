<?php

namespace Pak\Classes\Acl\Twig\TokenParser;

use Pak\Classes\Acl\Acl;
use Pak\Classes\Acl\Twig\Node\AccessNode;
use Pak\Classes\Acl\Twig\Node\NoAccessNode;
use Pak\Classes\Acl\Twig\Node\RestrNode;
use Twig_Node;
use Twig_Token;
use Twig_TokenParser;

/**
 * Restr block marks block with limited access rights.
 *
 * {% restr 'restricted_1' %}
 *   ...
 *   {% access 'r' %}
 *     ...
 *   {% endaccess %}
 *   ...
 * {% endrestr %}
 *
 * Code that is not included in any Access token will bi visible to anyone.
 *
 * @package Pak\Classes\Acl\Twig\TokenParser
 */
class RestrTokenParser extends Twig_TokenParser
{
    /** @var Acl */
    protected $acl;

    /**
     * @param Acl $acl
     */
    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param Twig_Token $token
     * @return Twig_Node
     */
    public function parse(Twig_Token $token)
    {
        $stream = $this->parser->getStream();

        /* Block name is mandatory */
        $restr_name = $stream->expect(Twig_Token::STRING_TYPE)->getValue();

        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideForEnd'));

        $nodeIterator = $body->getIterator();
        $had_permission = false; // triggers when first AccessNode occur

        /* Iterate over nested AccessNode's and find ones user has access to */
        foreach ($nodeIterator as $node) {
            switch (true) {
                /* Enable AccessNode if there are enough permissions */
                case ($node instanceof AccessNode):
                    $node_permissions = $node->getAttribute('permissions');
                    try {
                        if ($this->acl->isBlockAllowed($restr_name, $node_permissions)) {
                            $node->enable();
                            $had_permission = true;
                        }
                    } catch (\Exception $e) {
                    }
                    break;

                /* Remember there was NoAccessNode and compile it in case no AccessNode were enabled */
                case ($node instanceof NoAccessNode):
                    $noAccessNode = $node;
                    break;
            }
        }

        /* Compile NoAccessNode in case there were no AccessNode */
        if (!$had_permission && isset($noAccessNode) && $noAccessNode instanceof NoAccessNode) {
            $noAccessNode->enable();
        }

        $stream->next();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new RestrNode($body);
    }

    /**
     * @param Twig_Token $token
     * @return bool
     */
    public function decideForEnd(Twig_Token $token)
    {
        return $token->test('endrestr');
    }

    /**
     * @param Twig_Token $token
     * @return bool
     */
    public function decideForFork(Twig_Token $token)
    {
        return $token->test(['access', 'endaccess']);
    }

    /**
     * Gets the tag name associated with this token parser.
     * @return string
     */
    public function getTag()
    {
        return 'restr';
    }
}
