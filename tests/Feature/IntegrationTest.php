<?php

declare(strict_types=1);

namespace johninamillion\ToDo\Tests\Feature;

use johninamillion\ToDo\Scanner;
use johninamillion\ToDo\Tests\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * IntegrationTest.
 *
 * @package johninamillion/todo
 * @covers Scanner
 */
final class IntegrationTest extends TestCase
{
    #[Test]
    public function it_finds_all_relevant_results(): void
    {
        $scanner = new Scanner([
            'paths' => [Fixtures::PATH]
        ]);

        $results = $scanner
            ->scanFiles()
            ->getResults();

        $this->assertNotEmpty($results, "Scanner should return results");

        $expectedPerFile = Fixtures::EXPECTED_RESULTS_PER_FILE;
        $files = Fixtures::FILES;

        $this->assertCount((count($files) * $expectedPerFile), $results, "Scanner should return results for all files");
    }

    #[Test]
    public function it_finds_all_relevant_results_by_username(): void
    {
        $scanner = new Scanner([
            'paths' => [Fixtures::PATH],
            'username' => 'john-doe'
        ]);

        $results = $scanner
            ->scanFiles()
            ->getResults();

        $this->assertNotEmpty($results, "Scanner should return results");

        $expectedPerFile = Fixtures::EXPECTED_RESULTS_PER_FILE / 2;
        $files = Fixtures::FILES;

        $this->assertCount((count($files) * $expectedPerFile), $results, "Scanner should return results for all files");
    }

    #[Test]
    public function it_finds_all_relevant_results_by_keyword(): void
    {
        $scanner = new Scanner([
            'paths' => [Fixtures::PATH],
            'keywords' => ['security']
        ]);

        $results = $scanner
            ->scanFiles()
            ->getResults();

        $this->assertNotEmpty($results, "Scanner should return individual todos");

        $expectedPerFile = 2;
        $files = Fixtures::FILES;

        $this->assertCount((count($files) * $expectedPerFile), $results, "Scanner should return individual todos for all files");
    }

    #[Test]
    public function it_finds_all_bugs(): void
    {
        $scanner = new Scanner([
            'paths' => [Fixtures::PATH],
            'keywords' => ['bug']
        ]);

        $results = $scanner
            ->scanFiles()
            ->getResults();

        $this->assertNotEmpty($results, "Scanner should return bugs");

        $expectedPerFile = Fixtures::EXPECTED_BUGS_PER_FILE;
        $files = Fixtures::FILES;

        $this->assertCount((count($files) * $expectedPerFile), $results, "Scanner should return bugs for all files");
    }

    #[Test]
    public function it_finds_all_fixes(): void
    {
        $scanner = new Scanner([
            'paths' => [Fixtures::PATH],
            'keywords' => ['fix']
        ]);

        $results = $scanner
            ->scanFiles()
            ->getResults();

        $this->assertNotEmpty($results, "Scanner should return fixes");

        $expectedPerFile = Fixtures::EXPECTED_FIXES_PER_FILE;
        $files = Fixtures::FILES;

        $this->assertCount((count($files) * $expectedPerFile), $results, "Scanner should return fixes for all files");
    }

    #[Test]
    public function it_finds_all_tests(): void
    {
        $scanner = new Scanner([
            'paths' => [Fixtures::PATH],
            'keywords' => ['test']
        ]);

        $results = $scanner
            ->scanFiles()
            ->getResults();

        $this->assertNotEmpty($results, "Scanner should return tests");

        $expectedPerFile = Fixtures::EXPECTED_TESTS_PER_FILE;
        $files = Fixtures::FILES;

        $this->assertCount((count($files) * $expectedPerFile), $results, "Scanner should return tests for all files");
    }

    #[Test]
    public function it_finds_all_todos(): void
    {
        $scanner = new Scanner([
            'paths' => [Fixtures::PATH],
            'keywords' => ['todo']
        ]);

        $results = $scanner
            ->scanFiles()
            ->getResults();

        $this->assertNotEmpty($results, "Scanner should return todos");

        $expectedPerFile = Fixtures::EXPECTED_TODOS_PER_FILE;
        $files = Fixtures::FILES;

        $this->assertCount((count($files) * $expectedPerFile), $results, "Scanner should return todos for all files");
    }
}
