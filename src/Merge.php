<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\{
    Set,
    Map,
};

final class Merge implements Provide
{
    private array $providers;

    public function __construct(Provide ...$providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(): Set
    {
        $keys = Set::of(PublicKey::class);

        foreach ($this->providers as $provide) {
            $keys = $keys->merge($provide());
        }

        return $keys
            ->toMapOf(
                'string',
                PublicKey::class,
                static function(PublicKey $key): \Generator {
                    yield (string) $key => $key; // key de-duplication
                },
            )
            ->toSetOf(
                PublicKey::class,
                static fn(string $_, PublicKey $key): \Generator => yield $key,
            );
    }
}
