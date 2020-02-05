<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\HttpTransport\Transport;
use Innmind\Http\{
    Message\Request\Request,
    Message\Method,
    ProtocolVersion,
};
use Innmind\Url\Url;
use Innmind\Immutable\{
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
    public function __invoke(): Set
    {
        $response = ($this->fulfill)(new Request(
            Url::of("https://github.com/{$this->name}.keys"),
            Method::get(),
            new ProtocolVersion(2, 0),
        ));

        return $response
            ->body()
            ->read()
            ->split("\n")
            ->filter(static fn(Str $key): bool => !$key->empty())
            ->toSetOf(
                PublicKey::class,
                static fn(Str $key): \Generator => yield new PublicKey($key->toString()),
            );
    }
}
