<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Merge,
    Provide,
    PublicKey,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class MergeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Provide::class,
            new Merge,
        );
    }

    public function testMerge()
    {
        $provide = new Merge(
            $provider1 = $this->createMock(Provide::class),
            $provider2 = $this->createMock(Provide::class),
        );
        $provider1
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of(
                new PublicKey('foo'),
                $baz = new PublicKey('baz'),
            ));
        $provider2
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of(
                $foo = new PublicKey('foo'),
                $bar = new PublicKey('bar'),
            ));

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertEquals([$foo, $baz, $bar], $keys->toList());
    }
}
