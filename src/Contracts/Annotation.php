<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Contracts;

interface Annotation
{
    /**
     * Collect the annotation metadata to a container that you wants.
     * @param string $className
     */
    public function collectClass(string $className): void;

    /**
     * Collect the annotation metadata to a container that you wants.
     * @param string      $className
     * @param string|null $target
     */
    public function collectMethod(string $className, ?string $target): void;

    /**
     * Collect the annotation metadata to a container that you wants.
     * @param string      $className
     * @param string|null $target
     */
    public function collectProperty(string $className, ?string $target): void;
}
