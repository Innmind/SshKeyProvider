<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\SetInterface;

final class Cache implements Provide
{
    private $provide;
    private $keys;

    public function __construct(Provide $provide)
    {
        $this->provide = $provide;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(): SetInterface
    {
        return $this->keys ?? $this->keys = ($this->provide)();
    }
}
