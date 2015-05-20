<?php

namespace TPro\Slim\Acl\Twig\TokenParser;

use TPro\Slim\Acl\Twig\Node\AccessNode;
use Twig_Node;
use Twig_Token;
use Twig_TokenParser;

/**
 * Access token must be wrapped in Restr token.
 * Access token contains code that is only visible to users
 * that has necessary permissions to current Restr Token.
 *
 * {% restr <block_name> %}
 *   {% access <permissions> %}
 *   {% endaccess %}
 * {% endrestr %}
 *
 * <permissions> is PERMISSIONS_DELIMITER-separated string.
 *
 * @package Pak\Classes\Acl\Twig\TokenParser
 */
class AccessTokenParser extends Twig_TokenParser
{
    const PERMISSIONS_DELIMITER = ',';

    /**
     * @param Twig_Token $token
     * @return Twig_Node
     */
    public function parse(Twig_Token $token)
    {
        $stream = $this->parser->getStream();

        /* Permission string is mandatory */
        $permissionToken = $stream->expect(Twig_Token::STRING_TYPE);

        /* Permissions are stored in a delimiter-separated string */
        $permissions = explode(self::PERMISSIONS_DELIMITER, $permissionToken->getValue());

        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideForEnd'));
        $stream->next()->getValue();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new AccessNode($body, $permissions);
    }

    /**
     * @param Twig_Token $token
     * @return bool
     */
    public function decideForEnd(Twig_Token $token)
    {
        return $token->test('endaccess');
    }

    /**
     * Gets the tag name associated with this token parser.
     * @return string
     */
    public function getTag()
    {
        return 'access';
    }
}
