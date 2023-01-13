<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Server\Control\Server\{
    Processes,
    Command,
};
use Innmind\Url\Path;
use Innmind\Immutable\Set;

final class Local implements Provide
{
    private Processes $processes;
    private Path $sshFolder;

    public function __construct(Processes $processes, Path $sshFolder)
    {
        $this->processes = $processes;
        $this->sshFolder = $sshFolder;
    }

    public function __invoke(): Set
    {
        $key = $this
            ->processes
            ->execute(
                Command::foreground('cat')
                    ->withArgument('id_rsa.pub')
                    ->withWorkingDirectory($this->sshFolder),
            );

        return $key
            ->wait()
            ->match(
                static fn() => Set::of(new PublicKey($key->output()->toString())),
                static fn() => Set::of(),
            );
    }
}
