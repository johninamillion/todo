<?php

declare(strict_types=1);

namespace johninamillion\ToDo\Tests\Feature;

use johninamillion\ToDo\Commands\ScanCommand;
use johninamillion\ToDo\Tests\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * SystemTest.
 *
 * @package johninamillion/todo
 * @covers ScanCommand
 */
final class SystemTest extends TestCase
{
    private string $cli;

    protected function setUp(): void
    {
        $this->cli = realpath(__DIR__ . '/../../bin/todo');
    }

    #[Test]
    public function it_returns_expected_output_from_cli(): void
    {
        $command = sprintf('%s %s', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, "Command should exit with code 0");

        $outputString = implode(PHP_EOL, $output);
        $this->assertStringContainsString('BUG', $outputString);
        $this->assertStringContainsString('FIX', $outputString);
        $this->assertStringContainsString('TEST', $outputString);
        $this->assertStringContainsString('TODO', $outputString);

        foreach (Fixtures::FILES as $file) {
            $this->assertStringContainsString($file, $outputString, "Output should contain file: {$file}");
        }
    }

    #[Test]
    public function it_fails_on_bug_when_need(): void
    {
        $command = sprintf('%s %s --bugs --fail-on-bugs', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(1, $exitCode, "Command should exit with code 1");
    }

    #[Test]
    public function it_fails_on_fixes_when_need(): void
    {
        $command = sprintf('%s %s --fixes --fail-on-fixes', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(1, $exitCode, "Command should exit with code 1");
    }

    #[Test]
    public function it_filters_by_user_argument(): void
    {
        $command = sprintf('%s %s --user=john-doe', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, "Command should exit with code 0");

        $outputString = implode("\n", $output);
        $this->assertStringContainsString('@john-doe', $outputString);
    }

    #[Test]
    public function it_filters_by_keyword_argument(): void
    {
        $command = sprintf('%s %s --keywords=security', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, "Command should exit with code 0");

        $outputString = implode("\n", $output);
        $this->assertStringContainsString('SECURITY', $outputString);
        $this->assertStringNotContainsString('BUG', $outputString);
        $this->assertStringNotContainsString('FIX', $outputString);
        $this->assertStringNotContainsString('TEST', $outputString);
        $this->assertStringNotContainsString('TODO', $outputString);
    }

    #[Test]
    public function it_filters_by_bugs(): void
    {
        $command = sprintf('%s %s --bugs', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, "Command should exit with code 0");

        $outputString = implode("\n", $output);
        $this->assertStringContainsString('BUG', $outputString);
        $this->assertStringNotContainsString('FIX', $outputString);
        $this->assertStringNotContainsString('TEST', $outputString);
        $this->assertStringNotContainsString('TODO', $outputString);
    }

    #[Test]
    public function it_filters_by_fixes(): void
    {
        $command = sprintf('%s %s --fixes', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, "Command should exit with code 0");

        $outputString = implode("\n", $output);
        $this->assertStringContainsString('FIX', $outputString);
        $this->assertStringNotContainsString('BUG', $outputString);
        $this->assertStringNotContainsString('TEST', $outputString);
        $this->assertStringNotContainsString('TODO', $outputString);
    }

    #[Test]
    public function it_filters_by_tests(): void
    {
        $command = sprintf('%s %s --tests', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, "Command should exit with code 0");

        $outputString = implode("\n", $output);
        $this->assertStringContainsString('TEST', $outputString);
        $this->assertStringNotContainsString('BUG', $outputString);
        $this->assertStringNotContainsString('FIX', $outputString);
        $this->assertStringNotContainsString('TODO', $outputString);
    }

    #[Test]
    public function it_filters_by_todos(): void
    {
        $command = sprintf('%s %s --todos', escapeshellcmd($this->cli), escapeshellarg(Fixtures::PATH));
        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, "Command should exit with code 0");

        $outputString = implode(PHP_EOL, $output);
        $this->assertStringContainsString('TODO', $outputString);
        $this->assertStringNotContainsString('BUG', $outputString);
        $this->assertStringNotContainsString('FIX', $outputString);
        $this->assertStringNotContainsString('TEST', $outputString);
    }
}
