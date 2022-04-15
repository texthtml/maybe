<?php declare(strict_types=1);

namespace TH\Maybe\Tests;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use TH\Maybe\Result;

final class Assert extends PHPUnitAssert
{
    /**
     * @param Result<mixed,mixed> $result
     */
    public static function assertResultUsed(Result $result): void
    {
        try {
            self::assertThat($result, new Constraint\HasBeen(used: true));
        } finally {
            $result->unwrapOr("use the result");
        }
    }

    /**
     * @param Result<mixed,mixed> $result
     */
    public static function assertResultNotUsed(Result $result): void
    {
        self::assertThat($result, new Constraint\HasBeen(used: false));

        $result->unwrapOr("use the result");
    }
}
