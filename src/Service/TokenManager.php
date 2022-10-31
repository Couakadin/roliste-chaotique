<?php

namespace App\Service;

use App\Entity\Token\Token;
use App\Entity\User\User;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenManager
{
    /**
     * @param ObjectManager $manager
     */
    public function __construct
    (
        public readonly ObjectManager $manager
    )
    {
    }

    /**
     * @param string $type
     * @param User|UserInterface $user
     *
     * @return Token|Exception
     */
    public function checkAndSend(string $type, User|UserInterface $user): Token|Exception
    {
        try {
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
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
