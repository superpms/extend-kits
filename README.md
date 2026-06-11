# superpms/extend-kits

`superpms/extend-kits` is the SuperPMS composer package that makes project-local kits discoverable at runtime. It mounts the project kits directory, reads kit manifests, loads per-kit bootstrap files, exposes kit metadata and state helpers, and provides small base utilities for kit code.

This package is framework infrastructure. It does not implement a business kit by itself; business kits live under the host project's `kits` directory, for example `server/kits/<vendor>/<name>`.

## Requirements

- PHP `8.1+`
- Composer PSR-4 autoload for namespace `pms\`
- SuperPMS lifecycle hooks from `superpms/basic`

## Installation

```bash
composer require superpms/extend-kits
```

The package declares `extra.pms` in `composer.json`. In a SuperPMS project, the vendor install hook uses that metadata to create `/kits` and copy the default `kit.json` template from `resource/kit.json`.

## What This Package Provides

- Boot lifecycle integration through `bin/autoload.php` and `pms\extend\kits\Setup`.
- `kitsRoot` path mounting based on `BootOptions::get_extend('kits', '/kits')`.
- Root `kit.json` loading and per-kit `define.php` / `autoload.php` inclusion.
- Manifest helpers through `pms\helper\kits\KitsRegistryCenter`.
- Typed access to `kit.json`, `kit.extra.json`, manage/customer config files, and service declarations through `pms\program\kits\KitFileSource`.
- The `pms\app\KitsApp` trait for kit classes that need to resolve their own kit path and `config.php`.

## Documentation

Start with [docs/00-index.md](docs/00-index.md).

The docs are organized by domain, module, how-to guide, internal mechanism, and reference material. They are based on the current package code plus server-side usage points under `server/app`, `server/core`, and `server/kits`.

## Important Boundaries

- The package defines the kit loading and manifest access layer; it does not own platform or tenant HTTP policy.
- Kit install state is stored in each kit's `kit.extra.json`, while tenant activation is stored by server-side code.
- `services` declarations are generic provider metadata. Actual provider dispatch is handled by server adapters and connectors.
- `manage.install` is parsed by `KitFileSource::getManageInstall()`, but current server install endpoints do not execute those actions.

## Development

Keep package changes inside this package and validate Markdown after documentation updates. At minimum, check that the files are UTF-8 text and that relative links resolve from the document that declares them.

When code changes are made, run the relevant PHP syntax or test checks for the changed files.

## License

SuperPMS is released under the Apache-2.0 license. See [LICENSE.txt](LICENSE.txt).
