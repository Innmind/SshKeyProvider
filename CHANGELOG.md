# Changelog

## [Unreleased]

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
