<?php

declare(strict_types=1);

/**
 * ©️ copyright 2025 - johninamillion
 * 🙏🏻 Yet a man is risen to pursue thee, and to seek thy soul: but the soul of my lord shall be bound in the bundle of life with the Lord thy God; and the souls of thine enemies, them shall he sling out, as out of the middle of a sling. - I Samuel 25:29, KJV.
 */

namespace johninamillion\ToDo;

use InvalidArgumentException;

/**
 * The configuration.
 *
 * @package johninamillion/todo
 * @since   0.1.0
 *
 * @property-read string[] $paths
 * @property-read string[] $extensions
 * @property-read string[] $exclude
 * @property-read string[] $keywords
 * @property-read string   $mention_prefix
 * @property-read string   $username
 * @property-read bool     $fail_on_bugs
 * @property-read bool     $fail_on_fixes
 * @property-read bool     $include_dotfiles
 * @property-read bool     $include_ignored
 * @property-read bool     $display_time
 * @property-read bool     $display_time_difference
 */
class Configuration
{
    public const array SUPPORTED_FILE_EXTENSIONS = [
        'bash', 'blade', 'css', 'htm', 'html', 'js', 'jsx', 'md', 'php', 'scss',
        'sh', 'ts', 'tsx', 'twig', 'vue', 'xml', 'yaml', 'yml', 'zsh',
    ];

    /**
     * @var array<string,mixed>
     */
    protected array $data;

    /**
     * Constructor.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(array $data = [])
    {
        // set default paths if none are provided
        if (empty($data['paths'])) {
            $data['paths'] = [getcwd()];
        }

        // validate paths
        array_map(static function ($path) {
            if (!is_string($path)) {
                throw new InvalidArgumentException('Path must be a string.');
            }
            if (!is_dir($path)) {
                throw new InvalidArgumentException(sprintf('Path "%s" does not exist.', $path));
            }
        }, (array)$data['paths']);

        // set an empty exclude array if none are provided
        if (empty($data['exclude'])) {
            $data['exclude'] = [];
        }

        // validate excluded paths
        array_map(static function ($path) use ($data): void {
            if (!is_string($path)) {
                throw new InvalidArgumentException('Path must be a string.');
            }
            if (in_array($path, (array)$data['paths'], true)) {
                throw new InvalidArgumentException(sprintf('Path "%s" cannot be excluded.', $path));
            }
        }, (array)$data['exclude']);

        // set default keywords if none are provided
        if (empty($data['keywords'])) {
            $data['keywords'] = [Result::BUG, Result::FIX, Result::TEST, Result::TODO];
        }

        $defaults = [
            'extensions' => static::SUPPORTED_FILE_EXTENSIONS,
            'mention_prefix' => '@',
            'username' => null,
            'fail_on_bugs' => false,
            'fail_on_fixes' => false,
            'include_dotfiles' => false,
            'include_ignored' => false,
            'display_time' => false,
        ];

        $this->data = array_merge($defaults, $data);
    }

    /**
     * Get configuration value.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {

        return $this->data[$key] ?? null;
    }

    /**
     * Abort setting configuration values.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        trigger_error('Setting configuration values is not allowed.', E_USER_ERROR);
    }

    /**
     * Check if configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {

        return isset($this->data[$key]);
    }

    /**
     * Get excluded directories.
     *
     * @return string[]
     */
    public function getExcludeDirs(): array
    {
        /** @var array<string> $exclude */
        $exclude = $this->data['exclude'];

        return $exclude;
    }

    /**
     * Get file extensions.
     *
     * @return string[]
     */
    public function getFileExtensions(): array
    {
        /** @var array<string> $extensions */
        $extensions = $this->data['extensions'];

        return $extensions;
    }

    /**
     * Get file paths.
     *
     * @return string[]
     */
    public function getFilePaths(): array
    {
        /** @var array<string> $paths */
        $paths = $this->data['paths'];

        return $paths;
    }

    /**
     * Check if dotfiles should be ignored.
     *
     * @return bool
     */
    public function ignoreDotFiles(): bool
    {

        return !$this->data['include_dotfiles'];
    }

    /**
     * Check if gitignore should be ignored.
     *
     * @return bool
     */
    public function includeGitIgnore(): bool
    {

        return !$this->data['include_ignored'];
    }

    /**
     * Check if the scanner should fail on bugs.
     *
     * @return bool
     */
    public function shouldFailOnBugs(): bool
    {

        return (bool)$this->data['fail_on_bugs'];
    }

    /**
     * Check if the scanner should fail on fixes.
     *
     * @return bool
     */
    public function shouldFailOnFixes(): bool
    {

        return (bool)$this->data['fail_on_fixes'];
    }

    /**
     * Check if the scanner should filter for username.
     *
     * @return bool
     */
    public function shouldFilterForUsername(): bool
    {

        return $this->data['username'] !== null;
    }

    /**
     * Check if timestamps should be included in results.
     *
     * @return bool
     */
    public function shouldIncludeTimestamps(): bool
    {

        return (bool)$this->data['display_time'];
    }

    /**
     * Get the username with mention prefix for matches.
     *
     * @return string|null
     */
    public function usernameForMatches(): ?string
    {
        /** @var string|null $username */
        $username = $this->data['username'];
        /** @var string $mentionPrefix */
        $mentionPrefix = $this->data['mention_prefix'];

        return $username !== null
            ? "{$mentionPrefix}{$username}"
            : null;
    }
}
