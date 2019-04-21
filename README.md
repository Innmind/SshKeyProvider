# Ssh key provider

| `develop` |
|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/SshKeyProvider/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/SshKeyProvider/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/SshKeyProvider/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/SshKeyProvider/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/SshKeyProvider/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/SshKeyProvider/build-status/develop) |

Small library to retrieve all public keys from different locations.

## Installation

```sh
composer require innmind/ssh-key-provider
```

## Usage

```php
use Innmind\OperatingSystem\Factory;
use Innmind\SshKeyProvider\{
    Cache,
    Merge,
    Local,
    Github,
};
use Innmind\Url\Path;

$os = Factory::build();
$provide = new Cache(
    new Merge(
        new Local(
            $os->control()->processes(),
            new Path($_SERVER['USER'].'/.ssh')
        ),
        new Github(
            $os->remote()->http(),
            'GithubUsername'
        )
    )
);

$sshKeys = $provide();
```

This example will retrieve all keys define in the `GithubUsername` account and the key `id_rsa.pub` on the local machine.

**Important**: when no `id_rsa.pub` file found `Local` will generate a new one.
