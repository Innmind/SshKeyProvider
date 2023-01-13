<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Local,
    Provide,
    PublicKey,
};
use Innmind\Server\Control\Server\{
    Processes,
    Process,
    Process\ExitCode,
    Process\Output,
    Process\Failed,
};
use Innmind\Url\Path;
use Innmind\Immutable\{
    Set,
    Either,
    SideEffect,
};
use PHPUnit\Framework\TestCase;

class LocalTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Provide::class,
            new Local(
                $this->createMock(Processes::class),
                Path::none(),
            ),
        );
    }

    public function testReturnExistingKey()
    {
        $provide = new Local(
            $processes = $this->createMock(Processes::class),
            Path::of('/somewhere'),
        );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return $command->toString() === "cat 'id_rsa.pub'" &&
                    '/somewhere' === $command->workingDirectory()->match(
                        static fn($directory) => $directory->toString(),
                        static fn() => null,
                    );
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::right(new SideEffect));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn($output = $this->createMock(Output::class));
        $output
            ->expects($this->once())
            ->method('toString')
            ->willReturn('foo');

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertCount(1, $keys);
        $this->assertSame('foo', $keys->toList()[0]->toString());
    }

    public function testReturnNothingWhenNoLocalKey()
    {
        $provide = new Local(
            $processes = $this->createMock(Processes::class),
            Path::of('/somewhere'),
        );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return $command->toString() === "cat 'id_rsa.pub'" &&
                    '/somewhere' === $command->workingDirectory()->match(
                        static fn($directory) => $directory->toString(),
                        static fn() => null,
                    );
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::left(new Failed(new ExitCode(1))));

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertCount(0, $keys);
    }
}
