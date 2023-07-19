<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class IfyTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider values
     */
    public function testIfyOk(mixed $value): void
    {
        $callback = static fn () => $value;

        Assert::assertEquals($value, Result\ify($callback)()->unwrap());
    }

    public function testIfyCheckedException(): void
    {
        Assert::assertEquals(
            new \Exception(
                "Failed to parse time string (nope) at position 0 (n): The timezone could not be found in the database",
            ),
            // @phpstan-ignore-next-line
            Result\ify(static fn () => new \DateTimeImmutable("nope"))()->unwrapErr(),
        );
    }

    public function testIfyUncheckedException(): void
    {
        try {
            // @phpstan-ignore-next-line
            Result\ify(static fn () => 1 / 0)();
            Assert::fail("An exception should have been thrown");
        } catch (\DivisionByZeroError $ex) {
            Assert::assertEquals(
                "Division by zero",
                $ex->getMessage(),
            );
        }
    }
}
