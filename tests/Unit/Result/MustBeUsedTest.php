<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Result\UnusedResultException;

final class MustBeUsedTest extends TestCase
{
    /**
     * @dataProvider resultsFactory
     * @param callable(): Result<mixed, mixed> $factory
     */
    public function testNotUsingAResultThrowAnExceptionWhenFreed(callable $factory): void
    {
        $this->expectException(UnusedResultException::class);

        (static function (callable $factory): void {
            $factory();
        })($factory);

        Assert::fail("The exception should have been thrown before that");
    }

    /**
     * @dataProvider resultsFactory
     * @param callable(): Result<mixed, mixed> $factory
     */
    public function testUsingAResultAvoidTheExceptionWhenFreed(callable $factory): void
    {
        (static function (callable $factory): void {
            $result = $factory();
            Assert::assertNull($result->unwrapOr(null));
        })($factory);
    }

    /**
     * @dataProvider resultsFactory
     * @param callable(): Result<mixed, mixed> $factory
     */
    public function testAClonedResultMustBeUsed(callable $factory): void
    {
        $this->expectException(UnusedResultException::class);

        (static function (callable $factory): void {
            /** @var callable(): Result<mixed, mixed> $factory */
            $result = $factory();
            Assert::assertNull($result->unwrapOr(null));

            clone $result;
        })($factory);

        Assert::fail("The exception should have been thrown before that");
    }

    /**
     * @dataProvider resultsFactory
     * @param callable(): Result<mixed, mixed> $factory
     */
    public function testAnUnserializedResultDontHaveToBeUsed(callable $factory): void
    {
        (static function (callable $factory): void {
            $result = $factory();
            Assert::assertNull($result->unwrapOr(null));

            /** @psalm-suppress UnusedFunctionCall */
            \unserialize(\serialize($result));
        })($factory);
    }

    /**
     * @return iterable<array{callable():Result<mixed,mixed>}>
     */
    public function resultsFactory(): iterable
    {
        yield "ok"  => [static fn (): Result => Result\ok(null)];
        yield "err" => [static fn (): Result => Result\err(null)];
    }
}
