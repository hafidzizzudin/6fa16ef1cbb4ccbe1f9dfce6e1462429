<?php

namespace Src\System;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Okta\JwtVerifier\JwtVerifier;
use Okta\JwtVerifier\JwtVerifierBuilder;

class Authenticator
{
    private string $jwtSecret;

    private bool $useDB;

    // private JwtVerifier $oktaJwtVerifier;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'];
        $this->useDB = $_ENV['AUTHENTICATOR'] === 'db';

        // build db verifier

        // build okta verifier
        $issuer = $_ENV['OKTA_ISSUER'];
        $clientID = $_ENV['OKTA_CLIENTID'];

        // $this->oktaJwtVerifier = (new JwtVerifierBuilder())
        //     ->setIssuer($issuer)
        //     ->setAudience('api://default')
        //     ->setClientId($clientID)
        //     ->build();
    }

    private function jwtVerify(string $token): string
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return $decoded->email;
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function verify(string $token): string
    {
        // if ($this->useDB)
        return $this->jwtVerify($token);

        // return $this->oktaJwtVerifier->verify($token);
    }
}
