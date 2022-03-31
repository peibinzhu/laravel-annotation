<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Annotation;

class ScanConfig
{
    /**
     * @var bool
     */
    private $cacheable;

    /**
     * @var array
     */
    private $config;

    /**
     * The paths should be scaned everytime.
     *
     * @var array
     */
    private $paths;

    /**
     * @var array
     */
    private $collectors;

    /**
     * @var array
     */
    private $ignoreAnnotations;

    /**
     * @var array
     */
    private $globalImports;

    /**
     * @var array
     */
    private $dependencies;

    /**
     * @var array
     */
    private $classMap;

    /**
     * @var null|ScanConfig
     */
    private static $instance;

    public function __construct(
        bool $cacheable,
        array $config,
        array $paths = [],
        array $dependencies = [],
        array $ignoreAnnotations = [],
        array $globalImports = [],
        array $collectors = [],
        array $classMap = []
    ) {
        $this->cacheable = $cacheable;
        $this->config = $config;
        $this->paths = $paths;
        $this->dependencies = $dependencies;
        $this->ignoreAnnotations = $ignoreAnnotations;
        $this->globalImports = $globalImports;
        $this->collectors = $collectors;
        $this->classMap = $classMap;
    }

    public static function instance(?array $config): self
    {
        if (self::$instance) {
            return self::$instance;
        }

        $config = $config['scan'] ?? [];
        return self::$instance = new self(
            $config['cacheable'] ?? false,
            $config,
            $config['paths'] ?? [],
            [],
            $config['ignore_annotations'] ?? [],
            $config['global_imports'] ?? [],
            $config['collectors'] ?? [],
            $config['class_map'] ?? []
        );
    }

    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function getCollectors(): array
    {
        return $this->collectors;
    }

    public function getIgnoreAnnotations(): array
    {
        return $this->ignoreAnnotations;
    }

    public function getGlobalImports(): array
    {
        return $this->globalImports;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getClassMap(): array
    {
        return $this->classMap;
    }
}
