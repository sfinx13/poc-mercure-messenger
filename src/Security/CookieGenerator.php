<?php

namespace App\Security;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\MicrosecondBasedDateConversion;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{
    protected array $topics = [];

    public function generate(): Cookie
    {
        $token = (new Builder(new JoseEncoder(), new MicrosecondBasedDateConversion()))
            ->withHeader('typ', 'JWT')
            ->withHeader('alg', 'HS256')
            ->withClaim('mercure', ['subscribe' => $this->topics])
            ->getToken(new Sha256(), InMemory::plainText('!ChangeMe!'))
            ->toString();

        return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure');
    }

    public function addTopic($topic): void
    {
        if (!in_array($topic, $this->topics, true)) {
            $this->topics[] = $topic;
        }
    }

    public function setTopics($topics): self
    {
        foreach ($topics as $topic) {
            $this->addTopic($topic);
        }

        return $this;
    }
}
