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
};
use Innmind\Url\Path;
use Innmind\Immutable\Set;
use function Innmind\Immutable\first;
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
                    $command->workingDirectory()->toString() === '/somewhere';
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait');
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
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
        $this->assertSame(PublicKey::class, (string) $keys->type());
        $this->assertCount(1, $keys);
        $this->assertSame('foo', first($keys)->toString());
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
                    $command->workingDirectory()->toString() === '/somewhere';
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait');
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $keys = $provide();

        $this->assertInstanceOf(Set::class, $keys);
        $this->assertSame(PublicKey::class, (string) $keys->type());
        $this->assertCount(0, $keys);
    }
}
