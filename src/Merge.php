<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class Merge implements Provide
{
    private $providers;

    public function __construct(Provide ...$providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(): SetInterface
    {
        $keys = Set::of('string');

        foreach ($this->providers as $provide) {
            $keys = $keys->merge($provide());
        }

        return $keys;
    }
}
