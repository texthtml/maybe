<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Provider;

trait Values
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function values(): iterable
    {
        yield "null" => [null];
        yield "int" => [42];
        yield "string" => ["H2G2"];
        yield "object" => [(object)[]];
        yield "datetime" => [new \DateTimeImmutable()];
    }
}
