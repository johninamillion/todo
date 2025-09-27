# Release Notes

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),  
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [0.1.0] - 2025-09-25

### Added
- Initial CLI command `todo` to scan source files for:
    - `TODO`
    - `FIX`
    - `BUG`
    - `TEST`
- Color-coded terminal output per tag (bug, fix, test, todo)
- Display of last modification time per entry (`--display-time`)
- Filtering by:
    - `--keywords`
    - `--user`
    - `--me` (via Git username)
    - `--bugs`, `--fixes`, `--tests`, `--todos`
- Exclusion of paths via `--exclude`
- Options to include dotfiles (`--include-dotfiles`) and Git-ignored files (`--include-ignored`)
- Exit code control:
    - `--fail-on-bugs`
    - `--fail-on-fixes`
- Supported comment formats:
    - `//` single-line comments in `.php`, `.js`, `.jsx`, `.ts`, `.tsx`, `.vue` files.
    - `#` single-line comments in `bash`, `sh`, `zsh`, `yml`, `yaml`, files.
    - `/* ... */` multi-line comments in `.php`, `.css`, `.scss`, `.js`, `.jsx`, `.ts`, `.tsx`, `.vue` files.
    - `{{-- ... --}}` multi-line comments in `.blade` files.
    - `{# ... #}` multi-line comments in `.twig` files.
    - `<!-- ... -->` multi-line comments in `.blade`, `.twig`, `.md`, `.html`, `.html` and `.xml` files.

### Fixed
- N/A

### Changed
- N/A

---
