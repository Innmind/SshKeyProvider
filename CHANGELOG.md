# Changelog

## [Unreleased]

### Changed

- Require php 8.1
- `Innmind\SshKeyProvider\Local` now uses the filesystem abstraction instead of using processes
- `Innmind\SshKeyProvider\PublicKey` constructor is now private, use `::of()` or `::maybe()` named constructors instead

### Removed

- Auto generation of a ssh key when none is find on the filesystem has been removed
