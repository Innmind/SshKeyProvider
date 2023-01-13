<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Local,
    Provide,
    PublicKey,
};
use Innmind\Filesystem\{
    Adapter\InMemory,
    File\File,
    File\Content,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class LocalTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Provide::class,
            Local::of(InMemory::new()),
        );
    }

    public function testReturnExistingKey()
    {
        $provide = Local::of(
            $adapter = InMemory::new(),
        );
        $adapter->add(File::named('id_rsa.pub', Content\Lines::ofContent('foo')));

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertCount(1, $keys);
        $this->assertSame('foo', $keys->toList()[0]->toString());
    }

    public function testReturnNothingWhenNoLocalKey()
    {
        $provide = Local::of(InMemory::new());

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertCount(0, $keys);
    }
}
