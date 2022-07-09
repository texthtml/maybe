<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;

final class FinalTest extends TestCase
{
    public function testCannotInstanciateThirdPartyResultOk(): void
    {
        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage("Class MyOk cannot extend final class TH\Maybe\Result\Ok");

        eval("class MyOk extends \TH\Maybe\Result\Ok {}");    }

    public function testCannotInstanciateThirdPartyResultErr(): void
    {
        self::markTestSkipped("Probably have to fork to test that this is a fatal error");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage("Class MyErr cannot extend final class TH\Maybe\Result\Err");

        eval("class MyErr extends \TH\Maybe\Result\Err {}");
    }

    public function testCannotInstanciateThirdPartyResult(): void
    {
        self::markTestSkipped("Don't know how to prevent that");

        // @phpstan-ignore-next-line Unreachable statement - code above always terminates.
        $this->expectErrorMessage(
            "Call to private TH\Maybe\Result::__construct() from scope TH\Maybe\Tests\Unit\Result\FinalTest",
        );

        eval("class MyResult implements \TH\Maybe\Result {}");
    }
}
