<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\Exception\DomainException;
use Innmind\Immutable\{
    Str,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class PublicKey
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @psalm-pure
     *
     * @throws DomainException
     */
    public static function of(string $value): self
    {
        return self::maybe($value)->match(
            static fn($self) => $self,
            static fn() => throw new DomainException($value),
        );
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    public static function maybe(string $value): Maybe
    {
        return Maybe::just(Str::of($value))
            ->map(static fn($key) => $key->trim())
            ->filter(static fn($key) => !$key->empty())
            ->map(static fn($key) => new self($key->toString()));
    }

    public function toString(): string
    {
        return $this->value;
    }
}
