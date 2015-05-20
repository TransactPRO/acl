<?php

namespace TPro\Slim\Acl\Twig\TokenParser;

use TPro\Slim\Acl\Twig\Node\NoAccessNode;
use Twig_Node;
use Twig_Token;
use Twig_TokenParser;

/**
 * NoAccess token becomes visible only if no Access token
 * in current Restr block was enabled.
 *
 * Class NoAccessTokenParser
 * @package Pak\Classes\Acl\Twig\TokenParser
 */
class NoAccessTokenParser extends Twig_TokenParser
{
    /**
     * @param Twig_Token $token
     * @return Twig_Node
     */
    public function parse(Twig_Token $token)
    {
        $stream = $this->parser->getStream();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideForEnd'));
        $stream->next()->getValue();

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new NoAccessNode($body);
    }

    /**
     * @param Twig_Token $token
     * @return bool
     */
    public function decideForEnd(Twig_Token $token)
    {
        return $token->test('endnoaccess');
    }

    /**
     * Gets the tag name associated with this token parser.
     * @return string
     */
    public function getTag()
    {
        return 'noaccess';
    }

}
