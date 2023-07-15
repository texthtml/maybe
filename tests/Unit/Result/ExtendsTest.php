<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class ExtendsTest extends TestCase
{
    use Provider\Results;

    public function testOkIsExtendable(): void
    {
        $ok = new class ("Hello world!") extends Result\Ok {
            public function __toString(): string {
                return (string) $this->unwrap();
            }
        };

        Assert::assertSame("Hello world!", (string) $ok);
    }

    public function testErrIsExtendable(): void
    {
        $err = new class ("Hello world!") extends Result\Err {
            public function __toString(): string {
                return (string) $this->unwrapErr();
            }
        };

        Assert::assertSame("Hello world!", (string) $err);
    }

    /**
     * @dataProvider resultClasses
     */
    public function testConstructorsCannotBeOverriden(string $resultClass): void
    {
        $rc = new \ReflectionClass($resultClass);
        $rm = $rc->getMethod("__construct");

        Assert::assertTrue($rm->isFinal());
    }

    /**
     * @dataProvider resultMethods
     */
    public function testOkResultMethodsCannotBeOverriden(string $method): void
    {
        $rc = new \ReflectionClass(Result\Ok::class);

        $rm = $rc->getMethod($method);

        Assert::assertTrue($rm->isFinal());
    }

    /**
     * @dataProvider resultMethods
     */
    public function testErrResultMethodsCannotBeOverriden(string $method): void
    {
        $rc = new \ReflectionClass(Result\Err::class);

        $rm = $rc->getMethod($method);

        Assert::assertTrue($rm->isFinal());
    }
}
