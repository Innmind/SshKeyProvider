<?php
declare(strict_types = 1);

namespace Innmind\SshKeyProvider;

use Innmind\Immutable\{
    SetInterface,
    Set,
    MapInterface,
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
    public function __invoke(): SetInterface
    {
        $keys = Set::of(PublicKey::class);

        foreach ($this->providers as $provide) {
            $keys = $keys->merge($provide());
        }

        return Set::of(
            PublicKey::class,
            ...$keys
                ->reduce( // deduplicate the keys since not done automatically due to objects
                    Map::of('string', PublicKey::class),
                    static function(MapInterface $keys, PublicKey $key): MapInterface {
                        return $keys->put(
                            (string) $key,
                            $key
                        );
                    }
                )
                ->values()
        );
    }
}
