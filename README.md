# Maybe: Option & Result for PHP

Help yourself by not returning `null`, `false`, `-1` or throwing exception
for everything by using `Option` & `Result` instead.

Using those makes it harder to make mistake, make it easy to do common
operations on unknown returned values and can help static analysis tool detect
incorrect logic in PHP code.

# Installation

```bash
composer req texthtml/maybe
```

# Documentation

Read the [documentation](https://doc.maybe.texthtml.net) full API description, detailed explainations and usages.

# Usage

## Option

`TH\Maybe\Option` is a type that represents an optional value: every `Option` is either `Some` and contains a value, or `None`, and does not.

```php
/**
 * @return Option<float>
 */
function divide(float $numerator, float $denominator): Option {
    return match ($denominator) {
        0.0 => Option\none(),
        default => Option\some($numerator / $denominator),
    };
}

// The return value of the function is an option
$result = divide(2.0, 3.0);

// Pattern match to retrieve the value
if ($result->isSome()) {
    // The division was valid
    echo "Result: {$result->unwrap()}";
} else {
    // The division was invalid
    echo "Cannot divide by 0";
}
```

## Result

`TH\Maybe\Result` is a type that represents either success (`Ok`), containing the result of an operation or failure (`Err`), containing the reason of the failure.

```php
/**
 * @return Result<int,string>
 */
function parse_version(string $header): Result {
    return match ($header[0] ?? null) {
        null => Result\err("invalid header length"),
        "1" => Result\ok(1),
        "2" => Result\ok(2),
        default => Result\err("invalid version"),
    };
}

$version = parse_version("1.x");
if ($version->isOk()) {
    echo "working with version: {$version->unwrap()}";
} else {
    echo "error parsing header: {$version->unwrapErr()}";
}
// @prints working with version: 1
```
