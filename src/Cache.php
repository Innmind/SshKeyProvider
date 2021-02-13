<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\Set;

final class Cache implements Provide
{
    private Provide $provide;
    /** @var Set<PublicKey> */
    private ?Set $keys = null;

    public function __construct(Provide $provide)
    {
        $this->provide = $provide;
    }

    public function __invoke(): Set
    {
        return $this->keys ??= ($this->provide)();
    }
}
