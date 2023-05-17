<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Annotation;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;

class ScanConfig
{
    private static ?ScanConfig $instance = null;

    /**
     * @param bool   $cacheable
     * @param string $configDir
     * @param array  $paths The paths should be scanned everytime.
     * @param array  $dependencies
     * @param array  $ignoreAnnotations
     * @param array  $globalImports
     * @param array  $collectors
     * @param array  $classMap
     */
    public function __construct(
        private bool $cacheable,
        private string $configDir,
        private array $paths = [],
        private array $dependencies = [],
        private array $ignoreAnnotations = [],
        private array $globalImports = [],
        private array $collectors = [],
        private array $classMap = []
    ) {
    }

    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    public function getConfigDir(): string
    {
        return $this->configDir;
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

    public static function instance(string $environment, bool $scanCacheable = false): self
    {
        if (self::$instance) {
            return self::$instance;
        }

        [$config, $serverDependencies, $cacheable] = static::initConfig($environment, $scanCacheable);

        return self::$instance = new self(
            $cacheable,
            config_path(),
            $config['paths'] ?? [],
            $serverDependencies ?? [],
            $config['ignore_annotations'] ?? [],
            $config['global_imports'] ?? [],
            $config['collectors'] ?? [],
            $config['class_map'] ?? []
        );
    }

    private static function initConfig(string $environment, bool $scanCacheable = false): array
    {
        $config = [];
        $configItems = Container::getInstance()->get(Repository::class)->all();
        $cacheable = false;

        $serverDependencies = $configItems['dependencies'] ?? [];

        $config = static::allocateConfigValue($configItems['annotations'] ?? [], $config);

        if ($annotations = $configItems['annotations'] ?? null) {
            $cacheable = value($annotations['scan_cacheable'] ?? $environment == 'production');
        }

        return [$config, $serverDependencies, $scanCacheable || $cacheable];
    }

    private static function allocateConfigValue(array $content, array $config): array
    {
        if (!isset($content['scan'])) {
            return [];
        }

        foreach ($content['scan'] as $key => $value) {
            if (!isset($config[$key])) {
                $config[$key] = [];
            }
            if (!is_array($value)) {
                $value = [$value];
            }
            $config[$key] = array_merge($config[$key], $value);
        }
        return $config;
    }
}
