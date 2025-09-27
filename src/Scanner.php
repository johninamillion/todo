<?php

declare(strict_types=1);

/**
 * ©️ copyright 2025 - johninamillion
 * 🙏🏻 But he is in one mind, and who can turn him? and what his soul desireth, even that he doeth. - Job 23:13, KJV.
 */

namespace johninamillion\ToDo;

use InvalidArgumentException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use johninamillion\ToDo\Results\Bug;
use johninamillion\ToDo\Results\Fix;
use johninamillion\ToDo\Results\Test;
use johninamillion\ToDo\Results\ToDo;

/**
 * The scanner.
 *
 * @package johninamillion/todo
 * @since   0.1.0
 */
class Scanner
{
    protected Configuration $config;

    protected Finder $finder;

    /** @var Result[] */
    protected array $results = [];

    /**
     * Constructor.
     *
     * @param array<string,mixed> $options
     */
    public function __construct(array $options = [])
    {
        $this->config = new Configuration($options);
    }

    /**
     * Get Configuration.
     *
     * @return Configuration
     */
    public function getConfig(): Configuration
    {

        return $this->config;
    }

    /**
     * Get Results.
     *
     * @return Result[]
     */
    public function getResults(): array
    {

        return $this->results;
    }

    /**
     * Scan files.
     *
     * @return static
     */
    public function scanFiles(): static
    {
        $finder = $this->setupFinder();

        foreach ($finder as $file) {
            $this->processFile($file);
        }

        return $this;
    }

    /**
     * Check if a line begins a keyword.
     *
     * @param  string      $line
     * @param  string|null $match
     * @return bool
     */
    protected function beginsWithKeyword(string $line, ?string &$match = null): bool
    {
        $line = strtolower($line);

        foreach ($this->config->keywords as $keyword) {
            $keyword = strtolower($keyword);

            if (str_starts_with($line, $keyword)) {
                $match = $keyword;

                return true;
            }
        }

        return false;
    }

    /**
     * Check if a line contains a keyword.
     *
     * @param  string      $line
     * @param  string|null $match
     * @return bool
     */
    protected function containsKeyword(string $line, ?string &$match = null): bool
    {
        $line = strtolower($line);

        foreach ($this->config->keywords as $keyword) {
            $keyword = strtolower($keyword);

            if (stripos($line, $keyword) !== false) {
                $match = $keyword;

                return true;
            }
        }

        return false;
    }

    /**
     * Check if a line contains a username.
     *
     * @param  string $content
     * @return bool
     */
    protected function containsUsername(string $content): bool
    {
        $mention = $this->config->usernameForMatches();

        return $mention !== null && stripos($content, $mention) !== false;
    }

    /**
     * Create Result by Type.
     *
     * @param  string|null $type
     * @param  string      $filePath
     * @param  int         $line
     * @param  string      $content
     * @param  int|null    $timestamp
     * @return Result
     */
    protected function createResult(?string $type, string $filePath, int $line, string $content, ?int $timestamp): Result
    {

        return match ($type) {
            'bug' => new Bug($filePath, $line, $content, $timestamp),
            'fix' => new Fix($filePath, $line, $content, $timestamp),
            'test' => new Test($filePath, $line, $content, $timestamp),
            'todo' => new ToDo($filePath, $line, $content, $timestamp),
            default => new Result($filePath, $line, $content, $timestamp)
        };
    }

    /**
     * Get Comment Tags.
     *
     * @param  string                                                                 $fileExtension
     * @return array<array{type: string, start: string, end?: string, part?: string}>
     */
    protected function getCommentTags(string $fileExtension): array
    {
        return match (strtolower($fileExtension)) {
            // scripts
            'php', 'js', 'ts', 'jsx', 'tsx', 'vue' => [
                ['type' => 'block', 'start' => '/*', 'end' => '*/', 'part' => '*'],
                ['type' => 'line', 'start' => '//'],
            ],
            // templates
            'blade' => [
                ['type' => 'block', 'start' => '{{--', 'end' => '--}}'],
                ['type' => 'block', 'start' => '<!--', 'end' => '-->'],
            ],
            'twig' => [
                ['type' => 'block', 'start' => '{#', 'end' => '#}'],
                ['type' => 'block', 'start' => '<!--', 'end' => '-->'],
            ],
            // html and xml
            'htm', 'html', 'xml' => [
                ['type' => 'block', 'start' => '<!--', 'end' => '-->'],
            ],
            // stylesheets
            'css', 'scss' => [
                ['type' => 'block', 'start' => '/*', 'end' => '*/', 'part' => '*'],
            ],
            // shell and yaml
            'sh', 'bash', 'zsh', 'yml', 'yaml' => [
                ['type' => 'line', 'start' => '#'],
            ],
            // markdown
            'md' => [
                ['type' => 'line', 'start' => '[//]:'],
                ['type' => 'block', 'start' => '<!--', 'end' => '-->'],
            ],
            default => []
        };
    }

    /**
     * Get Git Timestamp.
     *
     * @param  string     $filePath
     * @param  int<0,max> $line
     * @return int|null
     */
    protected function getGitTimestamp(string $filePath, int $line): ?int
    {
        $output = $this->runProcess(['git', 'blame', '-L', "{$line},{$line}", '--line-porcelain', $filePath], true);

        if ($output !== null) {
            foreach (explode("\n", $output) as $blameLine) {
                if (str_starts_with($blameLine, 'author-time ')) {

                    return (int)trim(substr($blameLine, 12));
                }
            }
        }

        return null;
    }

    /**
     * Get File Timestamp.
     *
     * @param  string   $filePath
     * @return int|null
     */
    protected function getFileTimestamp(string $filePath): ?int
    {
        if (file_exists($filePath)) {
            $timestamp = filemtime($filePath);

            return $timestamp !== false ? $timestamp : null;
        }

        return null;
    }

    /**
     * @param  string     $block
     * @param  string     $filePath
     * @param  string     $fileExtension
     * @param  int<1,max> $line
     * @return void
     */
    protected function processCommentBlock(string $block, string $filePath, string $fileExtension, int $line): void
    {
        $lines = explode(PHP_EOL, $block);

        foreach ($lines as $offset => $content) {
            $this->processCommentLine($content, $filePath, $fileExtension, $line + $offset);
        }
    }

    /**
     * @param  string     $comment
     * @param  string     $filePath
     * @param  string     $fileExtension
     * @param  int<1,max> $line
     * @return void
     */
    protected function processCommentLine(string $comment, string $filePath, string $fileExtension, int $line): void
    {
        $content = $this->sanitizeLineContent($comment, $fileExtension);

        // skip lines without any keyword
        if (!$this->beginsWithKeyword($content, $type)) {
            return;
        }

        // skip lines without a username if a username is set
        if ($this->config->shouldFilterForUsername() && !$this->containsUsername($content)) {
            return;
        }

        $timestamp = $this->config->shouldIncludeTimestamps()
            ? $this->getGitTimestamp($filePath, $line) ?? $this->getFileTimestamp($filePath)
            : null;

        $this->results[] = $this->createResult($type, $filePath, $line, $content, $timestamp);
    }

    /**
     * Process File.
     *
     * @param  SplFileInfo $file
     * @return void
     */
    protected function processFile(SplFileInfo $file): void
    {
        if (is_array($lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES))) {
            $fileExtension = $file->getExtension();
            $filePath = $file->getRealPath();

            // skip invalid file paths
            if ($filePath === false) {
                return;
            }

            $commentTags = $this->getCommentTags($fileExtension);
            $endTag = '';
            $blockContent = '';
            $blockLine = 1;
            $inBlock = false;

            foreach ($lines as $index => $content) {
                if (empty($content)) {
                    continue;
                }

                $content = trim($content);
                $line = $index + 1;

                // append content to the block if it's in a block
                if ($inBlock) {
                    $blockContent .= PHP_EOL . $content;

                    if (str_ends_with($content, $endTag)) {
                        $inBlock = false;
                        $this->processCommentBlock($blockContent, $filePath, $fileExtension, $blockLine);
                    }

                    continue;
                }

                foreach ($commentTags as $tag) {
                    if (!str_contains($content, $tag['start'])) {
                        continue;
                    }

                    // remove content before the comment tag
                    $content = substr($content, (int) strpos($content, $tag['start']));

                    // comment lines
                    if ($tag['type'] === 'line') {
                        $this->processCommentLine($content, $filePath, $fileExtension, $line);

                        break;
                    }

                    // comment blocks
                    if ($tag['type'] === 'block') {
                        if (!isset($tag['end'])) {
                            throw new InvalidArgumentException('Comment block tag must have an end tag.');
                        }

                        $endTag = $tag['end'];
                        $inBlock = true;
                        $blockContent = $content;
                        $blockLine = $line;

                        if (str_ends_with($blockContent, $endTag)) {
                            $inBlock = false;
                            $this->processCommentBlock($blockContent, $filePath, $fileExtension, $blockLine);
                        }

                        break;
                    }
                }
            }
        }
    }

    /**
     * Run the process.
     *
     * @param  array<string> $command
     * @param  bool          $allowFailure
     * @return string|null
     */
    protected function runProcess(array $command, bool $allowFailure = false): ?string
    {
        $process = new Process($command);
        $success = $process->run() === 0;

        if (!$success && !$allowFailure) {
            throw new ProcessFailedException($process);
        }

        return $success
            ? trim($process->getOutput())
            : null;
    }

    /**
     * Sanitize Line Content.
     *
     * @param  string $content
     * @param  string $fileExtension
     * @return string
     */
    protected function sanitizeLineContent(string $content, string $fileExtension): string
    {
        $content = trim($content);
        $commentTags = $this->getCommentTags($fileExtension);

        foreach ($commentTags as $tag) {
            if (str_starts_with($content, $tag['start'])) {
                $content = ltrim($content, ' ' . $tag['start']);
            }
            if ($tag['type'] === 'block' && str_starts_with($content, $tag['part'] ?? '')) {
                $content = ltrim($content, ' ' . ($tag['part'] ?? ''));
            }
        }

        return trim($content);
    }

    /**
     * Setup Finder.
     *
     * @return Finder
     */
    protected function setupFinder(): Finder
    {
        $extensions = $this->config->getFileExtensions();

        return (new Finder())->files()
            ->in($this->config->getFilePaths())
            ->exclude($this->config->getExcludeDirs())
            ->ignoreDotFiles($this->config->ignoreDotFiles())
            ->ignoreUnreadableDirs()
            ->ignoreVCSIgnored($this->config->includeGitIgnore())
            ->ignoreVCS(true)
            ->filter(static function (SplFileInfo $file) use ($extensions) {

                return empty($extensions) || in_array($file->getExtension(), $extensions, true);
            });
    }
}
