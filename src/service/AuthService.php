<?php

namespace Src\Service;

use Src\Repository\User\UserRepository;
use DateTimeImmutable;
use Exception;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Ramsey\Uuid\Uuid;
use Src\Entity\User;

class AuthService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function register(array $data): string
    {
        // check user with existing email
        $email = $data['email'];
        $user = $this->userRepository->getUserByEmail($email);
        if ($user) {
            throw new Exception("user with email $email already exist", 400);
        }

        // insert into repo
        $user = new User();
        $user->userID = Uuid::uuid4()->toString();
        $user->email = $email;
        $user->salt = bin2hex(random_bytes(10));
        $user->password = hash_hmac('sha256', $data['password'], $user->salt);

        $this->userRepository->insert($user->toArrayInsert());

        return $user->userID;
    }

    public function login(array $data): string
    {
        // get user
        $user = $this->userRepository->getUserByEmail($data['email']);

        // authenticate
        $user->authenticate($data['password']);

        return $this->generateToken($user);
    }

    private function generateToken(User $user): string
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm    = new Sha256();
        $signingKey   = InMemory::plainText('6fa16ef1cbb4ccbe1f9dfce6e1462429');

        $now   = new DateTimeImmutable();

        $token = $tokenBuilder
            // Configures the issuer (iss claim)
            ->issuedBy('http://levart.com')
            // Configures the audience (aud claim)
            ->permittedFor('http://levart.com')
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+1 hour'))
            // Configures a new claim, called "uid"
            ->withClaim('email', $user->email)
            // Builds a new token
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }
}
