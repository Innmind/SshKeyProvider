<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Merge,
    Provide,
    PublicKey,
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
            ->willReturn(Set::of(
                PublicKey::class,
                new PublicKey('foo'),
                $baz = new PublicKey('baz')
            ));
        $provider2
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of(
                PublicKey::class,
                $foo = new PublicKey('foo'),
                $bar = new PublicKey('bar')
            ));

        $keys = $provide();

        $this->assertInstanceOf(SetInterface::class, $keys);
        $this->assertSame(PublicKey::class, (string) $keys->type());
        $this->assertSame([$foo, $baz, $bar], $keys->toPrimitive());
    }
}
