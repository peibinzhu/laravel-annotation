<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Annotation;

use Illuminate\Support\Arr;
use PeibinLaravel\Di\Contracts\MetadataCollector as MetadataCollectorContract;

abstract class MetadataCollector implements MetadataCollectorContract
{
    /**
     * Subclass MUST override this property.
     * @var array
     */
    protected static $container = [];

    /**
     * Retrieve the metadata via key.
     * @param string $key
     * @param        $default
     * @return mixed
     */
    public static function get(string $key, $default = null): mixed
    {
        return Arr::get(static::$container, $key) ?? $default;
    }

    /**
     * Set the metadata to holder.
     * @param string $key
     * @param mixed  $value
     */
    public static function set(string $key, mixed $value): void
    {
        Arr::set(static::$container, $key, $value);
    }

    /**
     * Determine if the metadata exist.
     * If exist will return true, otherwise return false.
     */
    public static function has(string $key): bool
    {
        return Arr::has(static::$container, $key);
    }

    /**
     * Clear the metadata via key.
     * @param string|null $key
     * @return void
     */
    public static function clear(?string $key = null): void
    {
        if ($key) {
            Arr::forget(static::$container, [$key]);
        } else {
            static::$container = [];
        }
    }

    /**
     * Serialize the all metadata to a string.
     */
    public static function serialize(): string
    {
        return serialize(static::$container);
    }

    /**
     * Deserialize the serialized metadata and set the metadata to holder.
     */
    public static function deserialize(string $metadata): bool
    {
        static::$container = unserialize($metadata);
        return true;
    }

    public static function list(): array
    {
        return static::$container;
    }
}
