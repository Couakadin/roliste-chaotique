<?php

namespace App\Mercure;

use Exception;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{
    /**
     * @throws Exception
     */
    public function generate(): Cookie
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm    = new Sha256();
        $signingKey   = InMemory::plainText(random_bytes(32));

        $token = $tokenBuilder
            ->withClaim('mercure', ['subscribe' => ['*']])
            ->getToken($algorithm, $signingKey);

        return Cookie::create('mercureAuthorization', $token->toString(), 0, '/.well-known/mercure');
    }
}