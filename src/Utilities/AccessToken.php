<?php

namespace PassportMetaClaim\Utilities;

use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use DateTimeImmutable;

class AccessToken extends PassportAccessToken
{
    use AccessTokenTrait;

    public function convertToJWT()
    {
        $this->initJwtConfiguration();

        $jwt = $this->jwtConfiguration->builder(ChainedFormatter::default())
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->withClaim('test', 'test value');

        return $jwt->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }
}