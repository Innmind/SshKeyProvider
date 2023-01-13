<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\Set;

final class Merge implements Provide
{
    /** @var list<Provide> */
    private array $providers;

    /**
     * @no-named-arguments
     */
    public function __construct(Provide ...$providers)
    {
        $this->providers = $providers;
    }

    public function __invoke(): Set
    {
        /** @var Set<PublicKey> */
        $keys = Set::of();

        foreach ($this->providers as $provide) {
            $keys = $keys->merge($provide());
        }

        /** @var Set<PublicKey> */
        return $keys
            ->map(static fn($key) => $key->toString()) // key de-duplication
            ->map(PublicKey::of(...));
    }
}
