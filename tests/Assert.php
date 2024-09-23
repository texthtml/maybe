<?php declare(strict_types=1);

namespace TH\Maybe\Tests;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use PHPUnit\Framework\ExpectationFailedException;
use TH\Maybe\Result;

final class Assert extends PHPUnitAssert
{
    /**
     * @template T
     * @template E
     * @param Result<T,E> $result
     * @throws ExpectationFailedException
     */
    public static function assertResultUsed(Result $result): void
    {
        try {
            self::assertThat($result, new Constraint\HasBeen(used: true));
        } finally {
            $result->mapOr(static fn () => "use the Ok result", "use the Err result");
        }
    }

    /**
     * @template T
     * @template E
     * @param Result<T,E> $result
     * @throws ExpectationFailedException
     */
    public static function assertResultNotUsed(Result $result): void
    {
        self::assertThat($result, new Constraint\HasBeen(used: false));

        $result->mapOr(static fn () => "use the Ok result", "use the Err result");
    }
}
