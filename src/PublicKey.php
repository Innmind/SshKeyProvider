<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\Exception\DomainException;
use Innmind\Immutable\Str;

final class PublicKey
{
    private $value;

    public function __construct(string $value)
    {
        $value = Str::of($value)->trim();

        if ($value->empty()) {
            throw new DomainException;
        }

        $this->value = (string) $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
