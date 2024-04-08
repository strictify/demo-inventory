<?php

declare(strict_types=1);

/**
 * @template TClamp of int|float
 *
 * @param TClamp $number
 * @param TClamp $min
 * @param TClamp $max
 *
 * @return TClamp
 *
 * Remove when https://wiki.php.net/rfc/clamp is merged
 */
function clamp(int|float $number, int|float $min, int|float $max): int|float
{
    if ($number > $max) {
        return $max;
    }
    if ($number < $min) {
        return $min;
    }

    return $number;
}

/**
 * @template T
 *
 * @param iterable<array-key, T> $input
 * @param Closure(T): bool $criteria
 *
 * @return ?T
 */
function find_first(iterable $input, Closure $criteria)
{
    foreach ($input as $item) {
        if ($criteria($item)) {
            return $item;
        }
    }

    return null;
}

/**
 * Applied callback on each element of iterable, and returns Generator of results.
 *
 * Useful for big data processing, and cleaner solution than @see array_map()
 *
 * @template T
 * @template R
 *
 * @param iterable<T> $input
 * @param Closure(T): R $callback
 *
 * @return Generator<array-key, R>
 */
function iterable_map(iterable $input, Closure $callback): Generator
{
    foreach ($input as $item) {
        yield $callback($item);
    }
}

/**
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $input
 *
 * @return array<TK, TV>
 */
function iterable_to_array(iterable $input): array
{
    $out = [];
    foreach ($input as $key => $value) {
        $out[$key] = $value;
    }

    return $out;
}

/**
 * Function useful to generate `Did you mean...` type of messages when exceptions are thrown.
 * Uses 1/3 length tolerance like other cases inside Symfony.
 *
 * @param iterable<string> $haystack
 */
function closest(string $needle, iterable $haystack): ?string
{
    $tolerance = strlen($needle) / 3;
    $closestLevel = strlen($needle);
    $suggestion = null;
    foreach ($haystack as $item) {
        $distance = levenshtein($needle, $item);
        if ($distance < $closestLevel && $distance < $tolerance) {
            $closestLevel = $distance;
            $suggestion = $item;
        }
    }

    return $suggestion;
}

/**
 * @template T
 *
 * @param list<T> $input
 * @param Closure(T): string $comparator
 *
 * @return list<T>
 */
function array_unique_comparable(array $input, Closure $comparator): array
{
    $unique = [];
    foreach ($input as $item) {
        $identifier = $comparator($item);
        if (!isset($unique[$identifier])) {
            $unique[$identifier] = $item;
        }
    }

    return array_values($unique);
}

/**
 * This is a fix for original function that only does (string)$object comparisons: https://www.php.net/manual/en/function.array-unique.php
 *
 * @template TU of object
 *
 * @param array<array-key, TU> $entities
 *
 * @return list<TU>
 */
function array_unique_objects(array $entities): array
{
    $result = [];
    foreach ($entities as $entity) {
        if (!in_array($entity, $result, true)) {
            $result[] = $entity;
        }
    }

    return $result;
}

function unless(bool $condition, Closure $callback): void
{
    if (!$condition) {
        $callback();
    }
}

/**
 * @param list<?string> $parts
 */
function implode_non_empty(string $separator, array $parts): ?string
{
    $filtered = array_filter($parts, fn(?string $part) => (bool)$part);

    return implode($separator, $filtered) ?: null;
}

/**
 * @param non-empty-list<array-key> $lookup
 */
function any_key_exists(array $lookup, array $haystack): bool
{
    foreach ($lookup as $item) {
        if (array_key_exists($item, $haystack)) {
            return true;
        }
    }

    return false;
}
