<?php

namespace App\Service;

use App\Entity\Token\Token;
use App\Entity\User\User;
use Doctrine\Persistence\ObjectManager;
use Exception;

class TokenManager
{
    public function __construct(
        public readonly ObjectManager $manager
    )
    {
    }

    /**
     * @throws Exception
     */
    public function checkAndSend(string $type, User $user): Token
    {
        $token = $this->manager->getRepository(Token::class)
            ->findOneBy(['type' => $type, 'user' => $user]) ?: null;

        if ($token instanceof Token) {
            $token->renewToken();
            $token->renewExpiredAt();
        } else {
            $token = new Token($user, $type);

            $this->manager->persist($token);
        }

        $this->manager->flush();

        return $token;
    }
}
