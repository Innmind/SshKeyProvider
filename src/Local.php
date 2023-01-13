<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Filesystem\{
    Adapter,
    Name,
};
use Innmind\Immutable\Set;

final class Local implements Provide
{
    private Adapter $adapter;

    private function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function __invoke(): Set
    {
        return $this
            ->adapter
            ->get(Name::of('id_rsa.pub'))
            ->map(static fn($key) => $key->content()->toString())
            ->flatMap(PublicKey::maybe(...))
            ->match(
                static fn($key) => Set::of($key),
                static fn() => Set::of(),
            );
    }

    public static function of(Adapter $adapter): self
    {
        return new self($adapter);
    }
}
