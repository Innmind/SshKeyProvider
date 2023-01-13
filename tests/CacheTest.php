<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Cache,
    Provide,
    PublicKey,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Provide::class,
            Cache::of(
                $this->createMock(Provide::class),
            ),
        );
    }

    public function testCache()
    {
        $provide = Cache::of(
            $provider = $this->createMock(Provide::class),
        );
        $provider
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of(
                $bar = PublicKey::of('bar'),
            ));

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertSame([$bar], $keys->toList());
        $this->assertSame($keys, $provide());
    }
}
