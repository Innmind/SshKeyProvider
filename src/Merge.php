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
    private function __construct(Provide ...$providers)
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

    /**
     * @no-named-arguments
     */
    public static function of(Provide ...$providers): self
    {
        return new self(...$providers);
    }
}
