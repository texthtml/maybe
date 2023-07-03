<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Helpers;

use TH\Maybe\Result\Err;
use TH\Maybe\Result\Ok;

/**
 * @internal
 */
final class IgnoreUnusedResults
{
    /** @var \SplObjectStorage<\ReflectionProperty, \ArrayAccess<mixed,mixed>|null> */
    private \SplObjectStorage $unusedResults;

    /**
     * Don't check whether new Results are used or not.
     */
    public function __construct()
    {
        $this->unusedResults = new \SplObjectStorage();

        foreach ($this->properties() as $rp) {
            $this->unusedResults[$rp] = $rp->isInitialized()
                ? $rp->getValue()
                : null;

            $rp->setValue(null, new class implements \ArrayAccess {
                public function offsetExists(mixed $offset): bool
                {
                    return false;
                }

                public function offsetGet(mixed $offset): mixed
                {
                    return null;
                }

                public function offsetSet(mixed $offset, mixed $value): void
                {
                }

                public function offsetUnset(mixed $offset): void
                {
                }
            });
        }
    }

    /**
     * @return iterable<\ReflectionProperty>
     */
    private function properties(): iterable
    {
        foreach ([Ok::class, Err::class] as $className) {
            /** @phpstan-throws void */
            yield (new \ReflectionClass($className))->getProperty("toBeUsed");
        }
    }

    public function __destruct()
    {
        foreach ($this->unusedResults as $rp) {
            $rp->setValue(null, $this->unusedResults[$rp] ?? new \WeakMap());
        }
    }
}
