<?php
/*
 * Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
 *
 * Author: Ravi Patel
 * Website: https://rbsoft.org/downloads/sms-gateway
 *
 * This software is licensed, not sold. Buyers are granted a limited, non-transferable license
 * to use this software exclusively on a single domain, subdomain, or computer. Usage on
 * multiple domains, subdomains, or computers requires the purchase of additional licenses.
 *
 * Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
 * is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
 * or creating derivative works based on this software is strictly prohibited.
 *
 * Unauthorized use, reproduction, or distribution of this software may result in severe civil
 * and criminal penalties and will be prosecuted to the fullest extent of the law.
 *
 * For licensing inquiries or support, please visit https://support.rbsoft.org.
 */

namespace App;

use App\Contracts\JwtSubject;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;

class JwtAuth
{
    protected string $key;
    protected string $algorithm;

    public function __construct()
    {
        $this->key = base64_decode(Str::after(config('app.key'), 'base64:'));
        $this->algorithm = 'HS256';
    }

    public function createToken(JwtSubject $model, ?int $ttl = null): string
    {
        $payload = [
            'iss' => config('app.name'), // Issuer
            'iat' => time(), // Issued at: Time at which the JWT was issued; can be used to determine age of the JWT
            'nbf' => time(), // Not before: Time before which the JWT must not be accepted for processing
            'exp' => $ttl ? time() + $ttl : null, // Expire: Time after which the JWT expires
            'sub' => $model->getJwtIdentifier(), // Subject of the JWT
        ];

        return JWT::encode($payload, $this->key, $this->algorithm);
    }

    public function parseToken(string $token): ?int
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->algorithm));
            return $decoded->sub;
        } catch (Exception) {
            return null;
        }
    }
}
