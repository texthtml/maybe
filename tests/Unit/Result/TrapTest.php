<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class TrapTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider values
     */
    public function testTrapOk(mixed $value): void
    {
        $callback = static fn () => $value;

        Assert::assertEquals($value, Result\trap($callback)->unwrap());
    }

    public function testTrapCheckedException(): void
    {
        Assert::assertEquals(
            new \Exception(
                "Failed to parse time string (nope) at position 0 (n): The timezone could not be found in the database",
            ),
            // @phpstan-ignore-next-line
            Result\trap(static fn () => new \DateTimeImmutable("nope"))->unwrapErr(),
        );
    }

    public function testTrapUncheckedException(): void
    {
        try {
            // @phpstan-ignore-next-line
            Result\trap(static fn () => 1 / 0);
            Assert::fail("An exception should have been thrown");
        } catch (\DivisionByZeroError $ex) {
            Assert::assertEquals(
                "Division by zero",
                $ex->getMessage(),
            );
        }
    }
}
