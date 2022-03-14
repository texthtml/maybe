<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

class FinalTest extends TestCase
{
    public function testCannotInstanciateThirdPartyResult(): void
    {
        $this->expectErrorMessage(
            "Call to private TH\Maybe\Result::__construct() from scope TH\Maybe\Tests\Result\FinalTest",
        );

        // @phpstan-ignore-next-line
        new class () extends Result {
            public function expect(string $message): mixed
            {
                return null;
            }

            public function unwrap(): mixed
            {
                return null;
            }

            public function unwrapErr(): mixed
            {
                return null;
            }

            public function unwrapOrElse(callable $default): mixed
            {
                return null;
            }

            public function orElse(callable $right): Result
            {
                return $right(null);
            }

            public function mapErr(callable $callback): Result
            {
                return Result::ok($callback(null));
            }

            public function mapOrElse(callable $callback, callable $default): mixed
            {
                return $callback(null);
            }

            public function containsErr(mixed $value, bool $strict = true): bool
            {
                return false;
            }
        };
    }
}
