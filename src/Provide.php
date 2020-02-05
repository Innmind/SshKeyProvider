<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\Set;

interface Provide
{
    /**
     * @return Set<PublicKey>
     */
    public function __invoke(): Set;
}
