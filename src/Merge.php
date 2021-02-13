<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\Set;

final class Merge implements Provide
{
    /** @var list<Provide> */
    private array $providers;

    public function __construct(Provide ...$providers)
    {
        $this->providers = $providers;
    }

    public function __invoke(): Set
    {
        /** @var Set<PublicKey> */
        $keys = Set::of(PublicKey::class);

        foreach ($this->providers as $provide) {
            $keys = $keys->merge($provide());
        }

        /** @var Set<PublicKey> */
        return $keys
            ->toMapOf(
                'string',
                PublicKey::class,
                static function(PublicKey $key): \Generator {
                    yield $key->toString() => $key; // key de-duplication
                },
            )
            ->toSetOf(
                PublicKey::class,
                static fn(string $_, PublicKey $key): \Generator => yield $key,
            );
    }
}
