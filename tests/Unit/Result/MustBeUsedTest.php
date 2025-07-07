<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Result;
use TH\Maybe\Result\UnusedResultException;

final class MustBeUsedTest extends TestCase
{
    /**
     * @param callable(): Result<mixed, mixed> $factory
     */
     #[DataProvider('resultsFactory')]
    public function testNotUsingAResultThrowAnExceptionWhenFreed(callable $factory): void
    {
        $this->expectException(UnusedResultException::class);

        (static function (callable $factory): void {
            $factory();
        })($factory);

        Assert::fail("The exception should have been thrown before that");
    }

    /**
     * @param callable(): Result<mixed, mixed> $factory
     */
     #[DataProvider('resultsFactory')]
    public function testUsingAResultAvoidTheExceptionWhenFreed(callable $factory): void
    {
        (static function (callable $factory): void {
            $result = $factory();
            Assert::assertNull($result->unwrapOr(null));
        })($factory);
    }

    /**
     * @param callable(): Result<mixed, mixed> $factory
     */
     #[DataProvider('resultsFactory')]
    public function testAClonedResultMustBeUsed(callable $factory): void
    {
        $this->expectException(UnusedResultException::class);

        (static function (callable $factory): void {
            /** @var callable(): Result<mixed, mixed> $factory */
            $result = $factory();
            Assert::assertNull($result->unwrapOr(null));

            clone $result; // @phpstan-ignore expr.resultUnused
        })($factory);

        Assert::fail("The exception should have been thrown before that");
    }

    /**
     * @param callable(): Result<mixed, mixed> $factory
     */
     #[DataProvider('resultsFactory')]
    public function testAnUnserializedResultDontHaveToBeUsed(callable $factory): void
    {
        (static function (callable $factory): void {
            $result = $factory();
            Assert::assertNull($result->unwrapOr(null));

            \unserialize(\serialize($result));
        })($factory);
    }

    /**
     * @return iterable<array{callable():Result<mixed,mixed>}>
     */
    public static function resultsFactory(): iterable
    {
        yield "ok"  => [static fn (): Result => Result\ok(null)];
        yield "err" => [static fn (): Result => Result\err(null)];
    }
}
