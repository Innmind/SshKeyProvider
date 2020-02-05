<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\HttpTransport\Transport;
use Innmind\Http\{
    Message\Request\Request,
    Message\Method\Method,
    ProtocolVersion\ProtocolVersion,
};
use Innmind\Url\Url;
use Innmind\Immutable\{
    SetInterface,
    Set,
    Str,
};

final class Github implements Provide
{
    private Transport $fulfill;
    private string $name;

    public function __construct(Transport $fulfill, string $name)
    {
        if (Str::of($name)->empty()) {
            throw new \DomainException;
        }

        $this->fulfill = $fulfill;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(): SetInterface
    {
        $response = ($this->fulfill)(new Request(
            Url::fromString("https://github.com/{$this->name}.keys"),
            Method::get(),
            new ProtocolVersion(2, 0)
        ));

        return $response
            ->body()
            ->read()
            ->split("\n")
            ->filter(static function(Str $key): bool {
                return !$key->empty();
            })
            ->reduce(
                Set::of(PublicKey::class),
                static function(SetInterface $keys, Str $key): SetInterface {
                    return $keys->add(
                        new PublicKey((string) $key)
                    );
                }
            );
    }
}
