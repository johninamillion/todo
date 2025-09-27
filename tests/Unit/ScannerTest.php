<?php

declare(strict_types=1);

namespace Unit;

use johninamillion\ToDo\Configuration;
use johninamillion\ToDo\Scanner;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * ConfigurationTest.
 *
 * @package johninamillion/todo
 * @covers Scanner
 */
final class ScannerTest extends TestCase
{
    protected function getScanner(): Scanner
    {
        $config = new Configuration(['paths' => [__DIR__]]);
        return new Scanner($config->getFilePaths());
    }

    #[Test]
    public function it_gets_comment_tags_for_php(): void
    {
        $scanner = $this->getScanner();
        $tags = $this->invokeMethod($scanner, 'getCommentTags', ['php']);

        $this->assertNotEmpty($tags);
        $this->assertEquals('line', $tags[1]['type']);
        $this->assertEquals('//', $tags[1]['start']);
    }

    #[Test]
    public function it_sanitizes_line_content(): void
    {
        $scanner = $this->getScanner();

        $line = '// TODO @john something important';
        $result = $this->invokeMethod($scanner, 'sanitizeLineContent', [$line, 'php']);

        $this->assertSame('TODO @john something important', $result);
    }

    /**
     * Helper to call protected/private methods
     */
    protected function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
