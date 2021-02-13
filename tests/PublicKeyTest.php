<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    PublicKey,
    Exception\DomainException,
};
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
            ->forAll(Set\Unicode::lengthBetween(1, 100))
            ->then(function(string $value): void {
                $this->assertSame($value, (new PublicKey($value))->toString());
            });
    }

    public function testTrim()
    {
        $this
            ->forAll(Set\Unicode::lengthBetween(1, 128))
            ->then(function(string $value): void {
                $this->assertSame($value, (new PublicKey("\n".$value."\n"))->toString());
            });
    }

    public function testThrowWhenEmptyString()
    {
        $this->expectException(DomainException::class);

        new PublicKey(' ');
    }
}
