<?php

declare(strict_types=1);

namespace johninamillion\ToDo\Tests\Unit;

use InvalidArgumentException;
use johninamillion\ToDo\Configuration;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * ConfigurationTest.
 *
 * @package johninamillion/todo
 * @covers Configuration
 */
final class ConfigurationTest extends TestCase
{
    #[Test]
    public function it_builds_up_on_default_values(): void
    {
        $config = new Configuration(['paths' => [__DIR__]]);

        $this->assertIsArray($config->getFileExtensions());
        $this->assertSame(Configuration::SUPPORTED_FILE_EXTENSIONS, $config->getFileExtensions());
        $this->assertFalse($config->shouldFailOnBugs());
        $this->assertFalse($config->shouldFailOnFixes());
        $this->assertFalse($config->shouldFilterForUsername());
        $this->assertFalse($config->shouldIncludeTimestamps());
    }

    #[Test]
    public function it_throws_an_exception_with_an_invalid_path(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Configuration([
            'paths' => ['/invalid/path/does/not/exist'],
        ]);
    }

    #[Test]
    public function it_could_include_dot_files(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
            'include_dotfiles' => true,
        ]);

        $this->assertFalse($config->ignoreDotFiles());
    }

    #[Test]
    public function it_should_ignore_dot_files_by_default(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
        ]);

        $this->assertTrue($config->ignoreDotFiles());
    }

    #[Test]
    public function it_could_include_git_ignored(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
            'include_ignored' => true,
        ]);

        $this->assertFalse($config->includeGitIgnore());
    }

    #[Test]
    public function it_should_ignore_git_ignored_by_default(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
        ]);

        $this->assertTrue($config->includeGitIgnore());
    }

    #[Test]
    public function it_could_fail_on_bugs(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
            'fail_on_bugs' => true,
        ]);

        $this->assertTrue($config->shouldFailOnBugs());
    }

    #[Test]
    public function it_should_not_fail_on_bugs_by_default(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
        ]);

        $this->assertFalse($config->shouldFailOnBugs());
    }

    #[Test]
    public function it_could_fail_on_fixes(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
            'fail_on_fixes' => true,
        ]);

        $this->assertTrue($config->shouldFailOnFixes());
    }

    #[Test]
    public function it_should_not_fail_on_fixes_by_default(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
        ]);

        $this->assertFalse($config->shouldFailOnFixes());
    }

    #[Test]
    public function it_could_filter_for_username_when_given(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
            'username' => 'john',
        ]);

        $this->assertTrue($config->shouldFilterForUsername());
    }

    #[Test]
    public function it_should_not_filter_for_username_when_empty(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
        ]);

        $this->assertFalse($config->shouldFilterForUsername());
    }

    #[Test]
    public function it_could_display_time(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
            'display_time' => true,
        ]);

        $this->assertTrue($config->shouldIncludeTimestamps());
    }

    #[Test]
    public function it_should_not_display_time_by_default(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
        ]);

        $this->assertFalse($config->shouldIncludeTimestamps());
    }

    #[Test]
    public function it_returns_a_valid_username_mention_format(): void
    {
        $config = new Configuration([
            'paths' => [__DIR__],
            'username' => 'john',
        ]);

        $this->assertSame('@john', $config->usernameForMatches());
    }
}
