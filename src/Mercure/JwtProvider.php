<?php

namespace App\Mercure;

use Exception;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;

class JwtProvider implements TokenProviderInterface
{
    /**
     * @throws Exception
     */
    public function getJwt(): string
    {
        return $this->generate();
    }

    /**
     * @throws Exception
     */
    private function generate(): string
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm    = new Sha256();
        $signingKey   = InMemory::plainText(random_bytes(32));

        $token = $tokenBuilder
            ->withClaim('mercure', ['subscribe' => ['*']])
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }
}