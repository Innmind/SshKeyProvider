<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\PublicKey;
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class PublicKeyTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this
            ->forAll(Set\Strings::madeOf(Set\Unicode::any())->between(1, 100))
            ->filter(static fn($value) => $value === \trim($value))
            ->then(function(string $value): void {
                $this->assertSame($value, PublicKey::maybe($value)->match(
                    static fn($key) => $key->toString(),
                    static fn() => null,
                ));
            });
    }

    public function testTrim()
    {
        $this
            ->forAll(Set\Strings::madeOf(Set\Unicode::any())->between(1, 128))
            ->filter(static fn($value) => !\str_contains($value, "\n"))
            ->then(function(string $value): void {
                $this->assertSame($value, PublicKey::maybe("\n".$value."\n")->match(
                    static fn($key) => $key->toString(),
                    static fn() => null,
                ));
            });
    }

    public function testReturnNothingWhenEmptyString()
    {
        $this->assertNull(PublicKey::maybe(' ')->match(
            static fn($key) => $key,
            static fn() => null,
        ));
    }
}
