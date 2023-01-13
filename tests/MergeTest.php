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
            Merge::of(),
        );
    }

    public function testMerge()
    {
        $provide = Merge::of(
            $provider1 = $this->createMock(Provide::class),
            $provider2 = $this->createMock(Provide::class),
        );
        $provider1
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of(
                PublicKey::of('foo'),
                $baz = PublicKey::of('baz'),
            ));
        $provider2
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn(Set::of(
                $foo = PublicKey::of('foo'),
                $bar = PublicKey::of('bar'),
            ));

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertEquals([$foo, $baz, $bar], $keys->toList());
    }
}
