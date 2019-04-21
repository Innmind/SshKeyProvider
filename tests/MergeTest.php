<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Merge,
    Provide,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class MergeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Provide::class,
            new Merge
        );
    }

    public function testMerge()
    {
        $provide = new Merge(
            $provider1 = $this->createMock(Provide::class),
            $provider2 = $this->createMock(Provide::class)
        );
        $provider1
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of('string', 'foo'));
        $provider2
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of('string', 'bar'));

        $keys = $provide();

        $this->assertInstanceOf(SetInterface::class, $keys);
        $this->assertSame('string', (string) $keys->type());
        $this->assertSame(['foo', 'bar'], $keys->toPrimitive());
    }
}
