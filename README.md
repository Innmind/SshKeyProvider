# Ssh key provider

[![Build Status](https://github.com/Innmind/SshKeyProvider/workflows/CI/badge.svg?branch=master)](https://github.com/Innmind/SshKeyProvider/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/Innmind/SshKeyProvider/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/SshKeyProvider)
[![Type Coverage](https://shepherd.dev/github/Innmind/SshKeyProvider/coverage.svg)](https://shepherd.dev/github/Innmind/SshKeyProvider)

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
$provide = Cache::of(
    Merge::of(
        Local::of(
            $os->filesystem()->mount(Path::of($_SERVER['USER'].'/.ssh/')),
        ),
        Github::of(
            $os->remote()->http(),
            'GithubUsername',
        ),
    ),
);

$sshKeys = $provide();
```

This example will retrieve all keys define in the `GithubUsername` account and the key `id_rsa.pub` on the local machine.
