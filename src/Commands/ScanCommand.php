<?php

declare(strict_types=1);

/**
 * ©️ copyright 2025 - johninamillion
 * 🙏🏻 And he said unto her, Give me thy son. And he took him out of her bosom, and carried him up into a loft, where he abode, and laid him upon his own bed. - I Kings 17:19, KJV.
 */

namespace johninamillion\ToDo\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use johninamillion\Git\User;
use johninamillion\ToDo\Result;
use johninamillion\ToDo\Results\Bug;
use johninamillion\ToDo\Results\Fix;
use johninamillion\ToDo\Results\Test;
use johninamillion\ToDo\Results\ToDo;
use johninamillion\ToDo\Scanner;

/**
 * The 'scan' command.
 *
 * @package johninamillion/todo
 * @since   0.1.0
 */
#[AsCommand('scan', 'Scan source files for TODOs and FIXMEs.')]
class ScanCommand extends Command
{
    protected InputInterface $input;
    protected OutputInterface $output;
    protected Scanner $scanner;
    protected Terminal $terminal;

    /** {@inheritdoc} */
    protected function configure(): void
    {
        $this
            ->setName('scan')
            ->setDescription('Scan source files for TODOs, BUGs, FIXes, or TESTs in comments.')
            ->addArgument('path', InputArgument::OPTIONAL, 'Optional base directory to scan (defaults to current working directory).')
            ->addOption('exclude', 'e', InputOption::VALUE_OPTIONAL, 'Comma-separated list of directories to exclude from scan.')
            ->addOption('keywords', 's', InputOption::VALUE_OPTIONAL, 'Comma-separated list of keywords to search for in comments.')
            ->addOption('user', 'u', InputOption::VALUE_OPTIONAL, 'Only show entries assigned to the given username.')
            ->addOption('me', null, InputOption::VALUE_NONE, 'Only show entries assigned to your Git username.')
            ->addOption('bugs', null, InputOption::VALUE_NONE, 'Show entries marked with a bug tag.')
            ->addOption('fixes', null, InputOption::VALUE_NONE, 'Show entries marked with a fix tag.')
            ->addOption('tests', null, InputOption::VALUE_NONE, 'Show entries marked with a test tag.')
            ->addOption('todos', null, InputOption::VALUE_NONE, 'Show entries marked with a todo tag.')
            ->addOption('display-time', null, InputOption::VALUE_NONE, 'Display the last modification time per entry.')
            ->addOption('fail-on-bugs', null, InputOption::VALUE_NONE, 'Exit with failure if any bugs are found.')
            ->addOption('fail-on-fixes', null, InputOption::VALUE_NONE, 'Exit with failure if any fixes are found.')
            ->addOption('include-dotfiles', null, InputOption::VALUE_NONE, 'Include dotfiles in scan (e.g. .env, .github).')
            ->addOption('include-ignored', null, InputOption::VALUE_NONE, 'Include files and folders listed in .gitignore.');
    }

    /** {@inheritdoc} */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $output->getFormatter()
            ->setStyle('bug', new OutputFormatterStyle('red', null, ['bold']));
        $output->getFormatter()
            ->setStyle('fix', new OutputFormatterStyle('yellow', null, ['bold']));
        $output->getFormatter()
            ->setStyle('test', new OutputFormatterStyle('cyan', null, ['bold']));
        $output->getFormatter()
            ->setStyle('todo', new OutputFormatterStyle('white', null, ['bold']));
        $output->getFormatter()
            ->setStyle('warning', new OutputFormatterStyle('black', 'yellow', ['bold']));
        $output->getFormatter()
            ->setStyle('success', new OutputFormatterStyle('black', 'green', ['bold']));

        /** @var string|null $pathsOption */
        $pathsOption = $input->getArgument('path');
        /** @var string[] $paths */
        $paths = $pathsOption !== null
            ? [$pathsOption]
            : [];

        /** @var string|null $excludeOption */
        $excludeOption = $input->getOption('exclude');
        /** @var string[] $exclude */
        $exclude = $excludeOption !== null
            ? explode(',', $excludeOption)
            : [];

        // check if the user wants to scan for bugs, fixes, tests or todos
        $tags = [
            Result::BUG => $input->getOption('bugs'),
            Result::FIX => $input->getOption('fixes'),
            Result::TEST => $input->getOption('tests'),
            Result::TODO => $input->getOption('todos'),
        ];
        /** @var string|null $keywordsOption */
        $keywordsOption = $input->getOption('keywords');
        /** @var string[] $keywords */
        $keywords = $keywordsOption !== null
            ? explode(',', $keywordsOption)
            : [];
        /** @var string[] $keywords */
        $keywords = array_merge($keywords, array_keys(array_filter($tags)));

        // check if the user wants to scan for themselves
        $isMe = $input->getOption('me') || $input->getOption('user') === 'me';
        /** @var string|null $username */
        $username = $isMe ? (new User())->getUsername() : $input->getOption('user');

        /** @var bool $failOnBugs */
        $failOnBugs = $input->getOption('fail-on-bugs');
        /** @var bool $failOnFixes */
        $failOnFixes = $input->getOption('fail-on-fixes');
        /** @var bool $includeDotfiles */
        $includeDotfiles = $input->getOption('include-dotfiles');
        /** @var bool $includeIgnored */
        $includeIgnored = $input->getOption('include-ignored');
        /** @var bool $displayTime */
        $displayTime = $input->getOption('display-time');

        $scanner = new Scanner([
            'paths' => $paths,
            'exclude' => $exclude,
            'keywords' => $keywords,
            'username' => $username,
            'fail_on_bugs' => $failOnBugs,
            'fail_on_fixes' => $failOnFixes,
            'include_dotfiles' => $includeDotfiles,
            'include_ignored' => $includeIgnored,
            'display_time' => $displayTime,
        ]);

        $this->input = $input;
        $this->output = $output;
        $this->scanner = $scanner;
        $this->terminal = new Terminal();
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->scanner
            ->getConfig();
        $results = $this->scanner
            ->scanFiles()
            ->getResults();

        return !empty($results)
            ? $this->printResults($results, $config->shouldFailOnBugs(), $config->shouldFailOnFixes())
            : $this->nothingToDo($config->keywords);
    }

    /**
     * Print nothing to do.
     *
     * @param  array<string> $keywords
     * @return int
     */
    protected function nothingToDo(array $keywords): int
    {
        $this->writeBoxMessage('success', 'There is nothing to do.');

        return Command::SUCCESS;
    }

    /**
     * Print results.
     *
     * @param  array<Result> $results
     * @param  bool          $failOnBugs
     * @param  bool          $failOnFixes
     * @return int
     */
    protected function printResults(array $results, bool $failOnBugs = false, bool $failOnFixes = false): int
    {
        $this->output->writeln('');

        $amount = count($results);
        $hasBugs = false;
        $hasFixes = false;
        $hasTests = false;
        $hasTodos = false;

        foreach ($results as $result) {
            $hasBugs = $hasBugs || $result instanceof Bug;
            $hasFixes = $hasFixes || $result instanceof Fix;
            $hasTests = $hasTests || $result instanceof Test;
            $hasTodos = $hasTodos || $result instanceof ToDo;

            $file = $result->file;
            $line = $result->line;
            $fileInfo = "{$file}:{$line}";
            $fileInfoLength = strlen($fileInfo);

            $message = rtrim($result->message, '.');
            $messageLength = strlen($message);

            $time = $result->timestamp ? date('d.m.Y H:i:s - ', $result->timestamp) : '';
            $timeLength = $result->timestamp ? strlen($time) : 0;

            $totalLength = $this->terminal->getWidth() - 4;
            $dotsAmount = $totalLength - $fileInfoLength - $timeLength - $messageLength - 4;
            $dots = $dotsAmount > 0
                ? str_repeat('.', $dotsAmount)
                : '';

            $tag = match ($result::class) {
                Bug::class => 'bug',
                Fix::class => 'fix',
                Test::class => 'test',
                default => 'todo',
            };

            $output = "  <{$tag}>{$time}{$message} {$dots} {$fileInfo}</{$tag}>  ";
            $this->output->writeln($output);
        }

        $tag = match (true) {
            $hasBugs => 'error',
            $hasFixes => 'warning',
            default => 'success',
        };

        $this->writeBoxMessage($tag, "Found: {$amount} Matches");

        return ($failOnBugs && $hasBugs) || ($failOnFixes && $hasFixes)
            ? Command::FAILURE
            : Command::SUCCESS;
    }

    protected function writeBoxMessage(string $tag, string $message): void
    {
        $messageLength = strlen($message);
        $padding = 2;

        $totalLength = $messageLength + (2 * $padding);
        $space = str_repeat(' ', $totalLength);
        $paddingSpace = str_repeat(' ', $padding);

        $this->output->writeln('');
        $this->output->writeln("<{$tag}>{$space}</{$tag}>");
        $this->output->writeln("<{$tag}>{$paddingSpace}{$message}{$paddingSpace}</{$tag}>");
        $this->output->writeln("<{$tag}>{$space}</{$tag}>");
        $this->output->writeln('');
    }
}
