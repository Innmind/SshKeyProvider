<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\SetInterface;

interface Provide
{
    /**
     * @return SetInterface<string>
     */
    public function __invoke(): SetInterface;
}
