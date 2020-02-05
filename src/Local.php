<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Server\Control\Server\{
    Processes,
    Command,
};
use Innmind\Url\PathInterface;
use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class Local implements Provide
{
    private Processes $processes;
    private PathInterface $sshFolder;

    public function __construct(Processes $processes, PathInterface $sshFolder)
    {
        $this->processes = $processes;
        $this->sshFolder = $sshFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(): SetInterface
    {
        $key = $this
            ->processes
            ->execute(
                Command::foreground('cat')
                    ->withArgument('id_rsa.pub')
                    ->withWorkingDirectory((string) $this->sshFolder)
            )
            ->wait();

        if ($key->exitCode()->isSuccessful()) {
            return Set::of(
                PublicKey::class,
                new PublicKey((string) $key->output())
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
                    ->withWorkingDirectory((string) $this->sshFolder)
            )
            ->wait();

        return $this();
    }
}
