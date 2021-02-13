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

    /**
     * {@inheritdoc}
     */
    public function __invoke(): Set
    {
        $key = $this
            ->processes
            ->execute(
                Command::foreground('cat')
                    ->withArgument('id_rsa.pub')
                    ->withWorkingDirectory($this->sshFolder),
            );
        $key->wait();

        if ($key->exitCode()->successful()) {
            return Set::of(
                PublicKey::class,
                new PublicKey($key->output()->toString()),
            );
        }

        $this
            ->processes
            ->execute(
                Command::foreground('ssh-keygen')
                    ->withShortOption('t')
                    ->withArgument('rsa')
                    ->withShortOption('f')
                    ->withArgument('id_rsa')
                    ->withShortOption('N')
                    ->withArgument('')
                    ->withWorkingDirectory($this->sshFolder),
            )
            ->wait();

        return $this();
    }
}
