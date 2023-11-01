# Changelog

## 3.2.0 - 2023-11-01

### Changed

- Requires `innmind/http:~7.0`
- Requires `innmind/filesystem:~7.1`

## 3.1.0 - 2023-09-23

### Added

- Support for `innmind/immutable:~5.0`

### Removed

- Support for PHP `8.1`

## 3.0.0 - 2023-01-13

### Changed

- Require php 8.1
- `Innmind\SshKeyProvider\Local` now uses the filesystem abstraction instead of using processes
- `Innmind\SshKeyProvider\PublicKey` constructor is now private, use `::of()` or `::maybe()` named constructors instead
- All implementations of `Innmind\SshKeyProvider\Provide` constructors are now private, use `::of()` named constructor instead

### Removed

- Auto generation of a ssh key when none is find on the filesystem has been removed
