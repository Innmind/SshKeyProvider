<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\Set;

final class Cache implements Provide
{
    private Provide $provide;
    private Set $keys;

    public function __construct(Provide $provide)
    {
        $this->provide = $provide;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(): Set
    {
        return $this->keys ?? $this->keys = ($this->provide)();
    }
}
