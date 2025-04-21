<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Helpers;

use TH\Maybe\Result\Err;
use TH\Maybe\Result\Ok;
use TH\ObjectReaper\Reaper;

/**
 * @internal
 */
final class IgnoreUnusedResults
{
    public function __construct()
    {
        $this->clearReapers();
    }

    /**
     * @return iterable<Reaper>
     */
    private function reapers(): iterable
    {
        foreach ([Ok::class, Err::class] as $className) {
            /** @phpstan-throws void */
            $rm = (new \ReflectionClass($className))->getMethod("reapers");

            /**
             * @var iterable<Reaper> $reapers
             * @throws void
             */
            $reapers = $rm->invoke(null);

            yield from $reapers;
        }
    }

    private function clearReapers(): void
    {
        foreach ($this->reapers() as $reaper) {
            $reaper->forget();
        }
    }

    public function __destruct()
    {
        $this->clearReapers();
    }
}
