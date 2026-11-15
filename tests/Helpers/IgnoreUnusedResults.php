<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Helpers;

use TH\DocTest\Setup\CustomAssertions;
use TH\DocTest\Setup\CustomAssertions\Call;
use TH\Maybe\Option;
use TH\Maybe\Result;
use TH\ObjectReaper\Reaper;

/**
 * doctest setup making examples that leave a `Result` unused pass: after each
 * `self::assert*` call, the reapers of every `Result` reachable from the call's
 * arguments are forgotten, so an assertion marks its operands used — inline
 * temporaries as well as variables — and reassigning a variable holding an
 * already-asserted Result is fine
 *
 * @internal
 */
final class IgnoreUnusedResults implements CustomAssertions
{
    public function assertion(Call $call, \Closure $next): void
    {
        foreach ($call->arguments as $argument) {
            $this->forgetReapers($argument);
        }

        $next();
    }

    private function forgetReapers(mixed $value): void
    {
        if ($value instanceof Option && $value->isSome()) {
            // the Option itself carries no reaper, but its content may
            // (`Option\transpose(Some(Ok(…)))`)
            $value->map($this->forgetReapers(...));

            return;
        }

        if (!$value instanceof Result) {
            return;
        }

        $this->forgetResultReapers($value);
    }

    /**
     * @param Result<mixed,mixed> $result
     */
    private function forgetResultReapers(Result $result): void
    {
        foreach ($this->reapersOf($result) as $reaper) {
            $reaper->forget();
        }
    }

    /**
     * @param Result<mixed,mixed> $result
     * @return iterable<Reaper>
     */
    private function reapersOf(Result $result): iterable
    {
        /** @phpstan-throws void */
        $rm = (new \ReflectionClass($result::class))->getMethod('reapers');

        /**
         * @var \WeakMap<object, Reaper> $reapers
         * @throws void
         */
        $reapers = $rm->invoke(null);

        /** @throws void */
        $reaper = $reapers[$result] ?? null;

        if (!$reaper instanceof Reaper) {
            return;
        }

        yield $reaper;
    }
}
