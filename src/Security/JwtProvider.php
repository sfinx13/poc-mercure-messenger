<?php

namespace App\Security;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\MicrosecondBasedDateConversion;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;

class JwtProvider implements TokenProviderInterface
{
    private const TOPICS = [ "user/{userid}/files", "message"];

    protected array $topics = [];

    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function getJwt(): string
    {
        $builder = (new Builder(new JoseEncoder(), new MicrosecondBasedDateConversion()))
            ->withHeader('typ', 'JWT')
            ->withHeader('alg', 'HS256')
            ->withClaim('mercure', ['publish' => $this->getTopics()])
            ->getToken(new Sha256(), InMemory::plainText($this->parameterBag->get('jwt_secret')));

        return $builder->toString();
    }

    private function getTopics()
    {
        return array_map(function (string $topic) {
            return $this->parameterBag->get('topic_url') . $topic;
        }, self::TOPICS);
    }
}
