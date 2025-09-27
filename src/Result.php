<?php

declare(strict_types=1);

/**
 * ©️ copyright 2025 - johninamillion
 * 🙏🏻 Now therefore, O king, come down according to all the desire of thy soul to come down; and our part shall be to deliver him into the king’s hand. - I Samuel 23:20, KJV.
 */

namespace johninamillion\ToDo;

/**
 * A result.
 *
 * @package johninamillion/todo
 * @since   0.1.0
 */
readonly class Result
{
    public const string BUG = 'bug';
    public const string FIX = 'fix';
    public const string TEST = 'test';
    public const string TODO = 'todo';

    /**
     * Constructor.
     *
     * @param string   $file
     * @param int      $line
     * @param string   $message
     * @param int|null $timestamp
     */
    public function __construct(
        public string $file,
        public int $line,
        public string $message,
        public ?int $timestamp,
    ) {}
}
