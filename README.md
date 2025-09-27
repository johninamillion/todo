# ToDo
### A powerful CLI tool to scan your source code for open tasks.

[![Tests](https://github.com/johninamillion/todo/actions/workflows/tests.yml/badge.svg)](https://github.com/johninamillion/todo/actions/workflows/tests.yml)
[![ToDos](https://github.com/johninamillion/todo/actions/workflows/todos.yml/badge.svg)](https://github.com/johninamillion/todo/actions/workflows/todos.yml)
[![License](https://img.shields.io/packagist/l/johninamillion/todo)](https://github.com/johninamillion/todo/blob/master/LICENSE)
[![Version](https://img.shields.io/packagist/v/johninamillion/todo)](https://packagist.org/packages/johninamillion/todo)

---

## Table of Contents

- [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
- [Development](#development)
- [License](#license)

---

## Installation

Install the package globally or per-project via Composer:

```shell
composer require johninamillion/todo
```

You can also link the CLI binary directly:

```shell
chmod +x bin/todo
```

## Features

- Detects `BUG`, `FIX`, `TODO`, and `TEST` annotations in comments.
- Filter by:
    - keyword (`--keywords`)
    - type (`--bugs`, `--fixes`, `--tests` and `--todos`)
    - user (`--user` or `--me`)
- Git-aware:
    - Honors .gitignore by default
    - Use your Git username with `--me`
- Optional flags:
    - `--fail-on-bugs`, `--fail-on-fixes`
    - `--display-time`
    - `--include-dotfiles`, `--include-ignored`

## Usage

Scan your current directory for all relevant tags:

```shell
./bin/todo
```

### Options

| Option               | Description                                        |
|----------------------|----------------------------------------------------|
| `[path]`             | Scan a specific directory (default: current)       |
| `--exclude=DIRS`     | Comma-separated list of directories to exclude     |
| `--keywords=...`     | Comma-separated list of keywords to match          |
| `--user=USERNAME`    | Only show entries assigned to a specific user      |
| `--me`               | Use your Git username to filter results            |
| `--bugs`             | Only show entries tagged with `BUG`                |
| `--fixes`            | Only show entries tagged with `FIX`                |
| `--tests`            | Only show entries tagged with `TEST`               |
| `--todos`            | Only show entries tagged with `TODO`               |
| `--display-time`     | Show the last modification time for each entry     |
| `--fail-on-bugs`     | Return non-zero exit code if any `BUG`s are found  |
| `--fail-on-fixes`    | Return non-zero exit code if any `FIX`es are found |
| `--include-dotfiles` | Include hidden files (e.g., `.env`, `.github`)     |
| `--include-ignored`  | Include files ignored by `.gitignore`              |

### Example Comment Format

Works with line comments:

```php
// BUG @john-doe This is a critical issue.
// FIX @jane-dev Applied hotfix.
// TODO @backend-team Improve validation.
// TEST @qa-user Add coverage for edge case.
```

Also works with block comments:

```php
/**
 * BUG @me Something is broken.
 * FIX @teammate Refactor needed.
 */
```

### Example Commands

Scan with bug-only filter:

```shell
./bin/todo --bugs
```

Scan with custom keywords and show timestamps:

```shell
./bin/todo --keywords=security,performance --display-time
```

Scan only your assigned items (via Git):

```shell
./bin/todo --me
```

Fail build on any FIX or BUG:

```shell
./bin/todo --fail-on-bugs --fail-on-fixes
```

### Configuration via composer.json

You may add these scripts for convenience:

```json
{
    "scripts": {
        "todo": "./bin/todo --display-time",
        "todo:me": "./bin/todo --me --display-time"
    }
}
```

## Development

### Analyze Code

To analyze your code for potential issues, you can run [phpstan](https://github.com/phpstan/phpstan):

```bash
composer code:analyse
```

### Apply Code-Styles

To ensure your code adheres to the coding standards, you can run
the [php-cs-fixer](https://github.com/php-cs-fixer/php-cs-fixer).

```bash
composer code:format
```

### Run Tests

To run the tests, make sure you have installed [phpunit](https://github.com/sebastianbergmann/phpunit) within the dev
dependencies and then run:

```bash
composer test
```

Check the Test Coverage:

```bash
composer test:coverage
```

---

## License

This package is licensed under the MIT License. See the LICENSE
file for full details.

<div align="center"><em>All Glory To God - The Father, The Son, and The Holy Spirit.</em></div>

