<?php

namespace Src\System;

use DateInterval;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Psr\SimpleCache\CacheInterface;
use UnexpectedValueException;

class Authenticator
{
    private string $jwtSecret;

    private bool $useDB;

    private ?array $keys;

    private string $clientID;

    private CacheInterface $cache;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'];
        $this->useDB = $_ENV['AUTHENTICATOR'] === 'db';

        if ($this->useDB)
            return;

        // build okta verifier
        $this->cache = new Repository(new FileStore(new Filesystem(), __DIR__ . "/../../.cache"));
        $issuer = $_ENV['OKTA_ISSUER'];
        $this->clientID = $_ENV['OKTA_CLIENTID'];

        $cached = $this->cache->get($this->clientID);
        if ($cached) {
            $this->keys = self::parseKeySet($cached);
            return;
        }

        $keysUrlSource = "$issuer/v1/keys";
        $this->keys = $this->getKeys($keysUrlSource);
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

    private function verifyAccessID(array $claims): bool
    {
        // currently only check for clientID
        if (!isset($claims['cid']) && $this->clientID == null) {
            return false;
        }

        if ($claims['cid'] != $this->clientID) {
            return false;
        }

        return true;
    }

    private function oktaVerify($token): string
    {
        try {
            $decoded =  $this->decode($token, $this->keys);
            $claims = $decoded->getClaims();

            if (!$this->verifyAccessID($claims)) {
                return '';
            }

            return $claims['sub'];
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function verify(string $token): string
    {
        if ($this->useDB)
            return $this->jwtVerify($token);

        return $this->oktaVerify($token);
    }

    // ==========================================================================================================================
    // code below is modification from https://github.com/okta/okta-jwt-verifier-php/blob/develop/src/Adaptors/FirebasePhpJwt.php
    // ==========================================================================================================================
    public function decode($jwt, $keys): OktaJwt
    {
        $keys = array_map(function ($key) {
            return new Key($key, 'RS256');
        }, $keys);
        $decoded = (array)JWT::decode($jwt, $keys);
        return (new OktaJwt($jwt, $decoded));
    }

    private function getKeys(string $urlSource): array
    {
        $client = new Client();
        $keys = json_decode($client->request('GET', $urlSource)->getBody()->getContents());
        $this->cache->set($this->clientID, $keys, DateInterval::createFromDateString('1 day'));

        return self::parseKeySet($keys);
    }

    private static function parseKeySet($source)
    {
        $keys = [];
        if (is_string($source)) {
            $source = json_decode($source, true);
        } elseif (is_object($source)) {
            if (property_exists($source, 'keys')) {
                $source = (array)$source;
            } else {
                $source = [$source];
            }
        }
        if (is_array($source)) {
            if (isset($source['keys'])) {
                $source = $source['keys'];
            }

            foreach ($source as $k => $v) {
                if (!is_string($k)) {
                    if (is_array($v) && isset($v['kid'])) {
                        $k = $v['kid'];
                    } elseif (is_object($v) && property_exists($v, 'kid')) {
                        $k = $v->{'kid'};
                    }
                }
                try {
                    $v = self::parseKey($v);
                    $keys[$k] = $v;
                } catch (UnexpectedValueException $e) {
                    //Do nothing
                }
            }
        }
        if (0 < count($keys)) {
            return $keys;
        }
        throw new UnexpectedValueException('Failed to parse JWK');
    }

    private static function parseKey($source)
    {
        if (!is_array($source)) {
            $source = (array)$source;
        }
        if (!empty($source) && isset($source['kty']) && isset($source['n']) && isset($source['e'])) {
            switch ($source['kty']) {
                case 'RSA':
                    if (array_key_exists('d', $source)) {
                        throw new UnexpectedValueException('Failed to parse JWK: RSA private key is not supported');
                    }

                    $pem = self::createPemFromModulusAndExponent($source['n'], $source['e']);
                    $pKey = openssl_pkey_get_public($pem);
                    if ($pKey !== false) {
                        return $pKey;
                    }
                    break;
                default:
                    //Currently only RSA is supported
                    break;
            }
        }

        throw new UnexpectedValueException('Failed to parse JWK');
    }

    private static function encodeLength($length)
    {
        if ($length <= 0x7F) {
            return chr($length);
        }

        $temp = ltrim(pack('N', $length), chr(0));
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }

    private static function createPemFromModulusAndExponent($n, $e)
    {
        $modulus = JWT::urlsafeB64Decode($n);
        $publicExponent = JWT::urlsafeB64Decode($e);


        $components = array(
            'modulus' => pack('Ca*a*', 2, self::encodeLength(strlen($modulus)), $modulus),
            'publicExponent' => pack('Ca*a*', 2, self::encodeLength(strlen($publicExponent)), $publicExponent)
        );

        $RSAPublicKey = pack(
            'Ca*a*a*',
            48,
            self::encodeLength(strlen($components['modulus']) + strlen($components['publicExponent'])),
            $components['modulus'],
            $components['publicExponent']
        );


        // sequence(oid(1.2.840.113549.1.1.1), null)) = rsaEncryption.
        $rsaOID = pack('H*', '300d06092a864886f70d0101010500'); // hex version of MA0GCSqGSIb3DQEBAQUA
        $RSAPublicKey = chr(0) . $RSAPublicKey;
        $RSAPublicKey = chr(3) . self::encodeLength(strlen($RSAPublicKey)) . $RSAPublicKey;

        $RSAPublicKey = pack(
            'Ca*a*',
            48,
            self::encodeLength(strlen($rsaOID . $RSAPublicKey)),
            $rsaOID . $RSAPublicKey
        );

        $RSAPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" .
            chunk_split(base64_encode($RSAPublicKey), 64) .
            '-----END PUBLIC KEY-----';

        return $RSAPublicKey;
    }
}
