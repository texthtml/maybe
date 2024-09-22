<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Helpers;
use TH\Maybe\Tests\Provider;

final class ExtendsTest extends TestCase
{
    use Provider\Results;

    public function testOkIsExtendable(): void
    {
        $ok = new Helpers\ExtendedOk("Hello world!");

        Assert::assertSame("Hello world!", (string) $ok);
    }

    public function testErrIsExtendable(): void
    {
        $err = new Helpers\ExtendedErr("Hello world!");

        Assert::assertSame("Hello world!", (string) $err);
    }

    /**
     * Allowing overriding constructors would make the "Must be used" feature unsafe
     *
     * @dataProvider resultClasses
     * @param class-string<Result<mixed,mixed>> $resultClass
     * @throws \ReflectionException
     */
    public function testConstructorsCannotBeOverriden(string $resultClass): void
    {
        $rc = new \ReflectionClass($resultClass);
        $rm = $rc->getMethod("__construct");

        Assert::assertTrue($rm->isFinal());
    }

    /**
     * @dataProvider resultMethods
     * @throws \ReflectionException
     */
    public function testOkResultMethodsCannotBeOverriden(string $method): void
    {
        $rc = new \ReflectionClass(Result\Ok::class);

        $rm = $rc->getMethod($method);

        Assert::assertTrue($rm->isFinal());
    }

    /**
     * @dataProvider resultMethods
     * @throws \ReflectionException
     */
    public function testErrResultMethodsCannotBeOverriden(string $method): void
    {
        $rc = new \ReflectionClass(Result\Err::class);

        $rm = $rc->getMethod($method);

        Assert::assertTrue($rm->isFinal());
    }
}
