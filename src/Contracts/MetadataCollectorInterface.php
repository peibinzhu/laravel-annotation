<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Contracts;

interface MetadataCollectorInterface
{
    /**
     * Retrieve the metadata via key.
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null);

    /**
     * Set the metadata to holder.
     * @param string $key
     * @param mixed  $value
     */
    public static function set(string $key, mixed $value): void;

    /**
     * Clear the metadata via key.
     * @param string|null $key
     */
    public static function clear(?string $key = null): void;

    /**
     * Serialize the all metadata to a string.
     */
    public static function serialize(): string;

    /**
     * Deserialize the serialized metadata and set the metadata to holder.
     * @param string $metadata
     * @return bool
     */
    public static function deserialize(string $metadata): bool;

    /**
     * Return all metadata array.
     * @return array
     */
    public static function list(): array;
}
