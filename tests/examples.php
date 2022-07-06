#!/usr/bin/env php
<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use TH\Maybe\Tests\Attributes\ExamplesSetup;

include_once __DIR__ . "/../vendor/autoload.php";

(new SingleCommandApplication())
    ->setName("Test code examples")
    ->setVersion("1.0.0")
    ->setHelp("Execute each code bloc found in comments to make sure they are correct")
    ->addArgument(
        "dirs",
        InputArgument::IS_ARRAY,
        "Folders to look for PHP files with code bloc examples",
        default: ["src"],
    )
    ->addOption(
        "bail",
        "b",
        InputOption::VALUE_NEGATABLE,
        "Stop after the first failure",
        default: false,
    )
    ->addOption(
        "root-dir",
        "r",
        InputOption::VALUE_REQUIRED,
        default: \realpath(__DIR__ . "/.."),
    )
    ->addOption(
        "filter",
        "f",
        InputOption::VALUE_REQUIRED,
        "Ony run code blocks whose names matche the filter",
        default: "",
    )
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $numberOfFailures = 0;
        $numberOfSuccesses = 0;

        $progressBar = $io->createProgressBar();
        $format = $progressBar::FORMAT_NORMAL . "_nomax";

        $progressBar->setMessage("");
        $progressBar->setFormatDefinition(
            $format,
            $progressBar::getFormatDefinition($format) . " %message%",
        );

        chdir($input->getOption("root-dir"));

        foreach (codeblocks($input->getArgument("dirs")) as $location => $codeblock) {
            $testName = "{$location["on"]}#{$location["number"]} ({$location["file"]}:{$location["line"]})";

            try {
                if (!str_contains($testName, $input->getOption("filter"))) {
                    continue;
                }

                $progressBar->setMessage($testName);
                $progressBar->advance();

                $handle = setup($location["source"]);

                ob_start();
                evalIsolated($codeblock);
                ob_get_clean();

                unset($handle);

                gc_collect_cycles();
                $progressBar->clear();
                $io->success($testName);

                $numberOfSuccesses++;
            } catch (Throwable $th) {
                $progressBar->clear();
                $error = [$testName];

                if ($output->isVerbose()) {
                    $error[] = $th;
                }

                if ($output->isVeryVerbose()) {
                    $error[] = $codeblock;
                }

                $io->error($error);

                $numberOfFailures++;

                if ($input->getOption("bail")) {
                    return Command::FAILURE;
                }
            }
        }

        if ($numberOfFailures === 0) {
            $io->success("All tests succeeded ($numberOfSuccesses)");

            return Command::SUCCESS;
        }

        $io->info("Number of success: $numberOfSuccesses");
        $io->error("Number of failures: $numberOfFailures");

        return Command::FAILURE;
    })
    ->run();

function evalIsolated($codeblock) {
    eval($codeblock);
}

function setup(\ReflectionClass|\ReflectionMethod|\ReflectionFunction $source): mixed
{
    $attributes = $source->getAttributes(ExamplesSetup::class, \ReflectionAttribute::IS_INSTANCEOF);

    foreach ($attributes as $attribute) {
        $setup = $attribute->newInstance()->setup;

        return new $setup();
    }

    if ($source instanceof \ReflectionMethod) {
        return setup($source->getDeclaringClass());
    }

    return null;
}

/**
 * @param array<string> $srcDirs
 * @return iterable<array{
 *   file: string,
 *   line: int,
 *   on: string,
 *   number: int,
 *   source: \ReflectionClass|\ReflectionMethod|\ReflectionFunction,
 * }, string>
 */
function codeblocks(array $srcDirs): iterable
{
    $stripSrcDirPattern = "/^" . preg_quote(getcwd(), "/") . "(\/*)/";

    foreach (comments($srcDirs) as $source => $comment) {
        $lines = explode(PHP_EOL, $comment);

        $number = 1;
        $lineNumber = $source->getStartLine() - count($lines);
        $buffer = [];
        $codeblockStartedAt = null;

        foreach ($lines as $line) {
            $lineNumber++;
            $line = ltrim($line);

            if ($line === "* ```") {
                if ($codeblockStartedAt !== null) {
                    $name = $source->getName();

                    if ($source instanceof ReflectionMethod) {
                        $name = "{$source->getDeclaringClass()->getName()}::$name(…)";
                    }

                    yield [
                        "file" => preg_replace($stripSrcDirPattern, "", $source->getFileName()),
                        "line" => $codeblockStartedAt,
                        "on" => $name,
                        "number" => $number++,
                        "source" => $source,
                    ] => implode(PHP_EOL, $buffer);
                    $codeblockStartedAt = null;
                } else {
                    $codeblockStartedAt = $lineNumber;
                }

                $buffer = [];
            } else if ($codeblockStartedAt !== null) {
                $buffer[] = preg_replace("/^\*( ?)/", "", $line);
            }
        }
    }
}

/**
 * @param array<string> $srcDirs
 * @return iterable<\ReflectionClass|\ReflectionMethod|\ReflectionFunction,string>
 */
function comments(array $srcDirs): iterable
{
    foreach (commentReflectables($srcDirs) as $reflection) {
        $comment = $reflection->getDocComment();

        if ($comment !== false) {
            yield $reflection => $comment;
        }
    }
}

/**
 * @param array<string> $srcDirs
 * @return iterable<\ReflectionClass|\ReflectionMethod|\ReflectionFunction>
 */
function commentReflectables(array $srcDirs): iterable
{
    $finder = new Finder();

    $files = $finder->files()->in($srcDirs)->name("*.php");

    foreach ($files as $file) {
        include_once $file->getPathname();
    }

    $interfaces = array_filter(
        get_declared_interfaces(),
        static fn (string $className) => str_starts_with($className, \TH\Maybe::class),
    );

    $classes = array_filter(
        get_declared_classes(),
        static fn (string $className) => str_starts_with($className, \TH\Maybe::class),
    );

    foreach (array_merge($interfaces, $classes) as $className) {
        $rc = new \ReflectionClass($className);

        yield $rc;

        foreach ($rc->getMethods() as $rm) {
            yield $rm;
        }
    }

    $functions = array_filter(
        get_defined_functions()["user"],
        static fn (string $functionName) => str_starts_with($functionName, strtolower(\TH\Maybe::class)),
    );

    foreach ($functions as $function) {
        $rf = new \ReflectionFunction($function);

        yield $rf;
    }
}
