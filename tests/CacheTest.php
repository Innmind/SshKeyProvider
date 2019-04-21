<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Cache,
    Provide,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Provide::class,
            new Cache(
                $this->createMock(Provide::class)
            )
        );
    }

    public function testCache()
    {
        $provide = new Cache(
            $provider = $this->createMock(Provide::class)
        );
        $provider
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of('string', 'bar'));

        $keys = $provide();

        $this->assertInstanceOf(SetInterface::class, $keys);
        $this->assertSame('string', (string) $keys->type());
        $this->assertSame(['bar'], $keys->toPrimitive());
        $this->assertSame($keys, $provide());
    }
}
