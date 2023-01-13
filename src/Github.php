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

    public function __invoke(): Set
    {
        return ($this->fulfill)(new Request(
            Url::of("https://github.com/{$this->name}.keys"),
            Method::get,
            ProtocolVersion::v20,
        ))
            ->map(static fn($success) => $success->response())
            ->map(
                static fn($response) => $response
                    ->body()
                    ->lines()
                    ->filter(static fn($line) => !$line->str()->empty())
                    ->map(static fn($line) => new PublicKey($line->toString())),
            )
            ->match(
                static fn($keys) => Set::of(...$keys->toList()),
                static fn() => Set::of(),
            );
    }
}
