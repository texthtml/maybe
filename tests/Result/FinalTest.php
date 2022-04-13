<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

class FinalTest extends TestCase
{
    public function testCannotInstanciateThirdPartyResultOk(): void
    {
        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage(
            "Class TH\Maybe\Result\Ok@anonymous cannot extend final class TH\Maybe\Result\Ok",
        );

        // @phpstan-ignore-next-line
        new class () extends Result\Ok {};
    }

    public function testCannotInstanciateThirdPartyResultErr(): void
    {
        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage(
            "Class TH\Maybe\Result\Err@anonymous cannot extend final class TH\Maybe\Result\Err",
        );

        // @phpstan-ignore-next-line
        new class () extends Result\Err {};
    }

    public function testCannotInstanciateThirdPartyResult(): void
    {
        self::markTestSkipped("Don't know how to prevent that");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage(
            "Call to private TH\Maybe\Result::__construct() from scope TH\Maybe\Tests\Result\FinalTest",
        );

        // @phpstan-ignore-next-line
        new class () implements Result {};
    }
}
