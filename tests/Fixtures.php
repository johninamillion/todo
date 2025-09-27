<?php

declare(strict_types=1);

namespace johninamillion\ToDo\Tests;

/**
 * Helper class for fixtures the tests.
 *
 * @package johninamillion/todo
 */
abstract class Fixtures
{
    public const int EXPECTED_BUGS_PER_FILE = 2;
    public const int EXPECTED_FIXES_PER_FILE = 2;
    public const int EXPECTED_TESTS_PER_FILE = 2;
    public const int EXPECTED_TODOS_PER_FILE = 2;
    public const int EXPECTED_RESULTS_PER_FILE = self::EXPECTED_BUGS_PER_FILE + self::EXPECTED_FIXES_PER_FILE + self::EXPECTED_TESTS_PER_FILE + self::EXPECTED_TODOS_PER_FILE;

    public const array FILES = [
        'blockLineSample.blade',
        'blockLineSample.css',
        'blockLineSample.html',
        'blockLineSample.twig',
        'blockSample.blade',
        'blockSample.css',
        'blockSample.html',
        'blockSample.js',
        'blockSample.php',
        'blockSample.twig',
        'lineSample.js',
        'lineSample.php',
        'lineSample.sh',
    ];

    public const string PATH = __DIR__ . '/fixtures';
}
