<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    PublicKey,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;
use Eris\{
    Generator,
    TestTrait,
};

class PublicKeyTest extends TestCase
{
    use TestTrait;

    public function testInterface()
    {
        $this
            ->forAll(Generator\string())
            ->when(static function(string $value): bool {
                return $value !== '';
            })
            ->then(function(string $value): void {
                $this->assertSame($value, (new PublicKey($value))->toString());
            });
    }

    public function testTrim()
    {
        $this
            ->forAll(Generator\string())
            ->when(static function(string $value): bool {
                return $value !== '';
            })
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
