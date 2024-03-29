<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\Exception\DomainException;
use Innmind\HttpTransport\Transport;
use Innmind\Http\{
    Request,
    Method,
    ProtocolVersion,
};
use Innmind\Url\Url;
use Innmind\Immutable\{
    Set,
    Str,
    Sequence,
};

final class Github implements Provide
{
    private Transport $fulfill;
    private string $name;

    private function __construct(Transport $fulfill, string $name)
    {
        if (Str::of($name)->empty()) {
            throw new DomainException;
        }

        $this->fulfill = $fulfill;
        $this->name = $name;
    }

    public function __invoke(): Set
    {
        /** @psalm-suppress InvalidArgument Due to the empty sequence */
        return ($this->fulfill)(Request::of(
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
                    ->map(static fn($line) => $line->toString())
                    ->flatMap(static fn($line) => PublicKey::maybe($line)->match(
                        static fn($key) => Sequence::of($key),
                        static fn() => Sequence::of(),
                    )),
            )
            ->match(
                static fn($keys) => Set::of(...$keys->toList()),
                static fn() => Set::of(),
            );
    }

    /**
     * @throws DomainException When the name is empty
     */
    public static function of(Transport $fulfill, string $name): self
    {
        return new self($fulfill, $name);
    }
}
