<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Annotation;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

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

    public static function instance(Application $container): self
    {
        if (self::$instance) {
            return self::$instance;
        }

        $configDir = rtrim($container->configPath(), '/');

        [$config, $serverDependencies, $cacheable] = static::initConfig($container);

        return self::$instance = new self(
            $cacheable,
            $configDir,
            $config['paths'] ?? [],
            $serverDependencies ?? [],
            $config['ignore_annotations'] ?? [],
            $config['global_imports'] ?? [],
            $config['collectors'] ?? [],
            $config['class_map'] ?? []
        );
    }

    private static function initConfig(Application $container): array
    {
        $config = [];

        $configFromProviders = $container->get(Repository::class)->all();

        $serverDependencies = $configFromProviders['dependencies'] ?? [];

        $annotationConfig = $configFromProviders['annotations'] ?? [];

        $config = static::allocateConfigValue($annotationConfig, $config);

        $cacheable = value($annotationConfig['scan_cacheable'] ?? $container->environment() == 'production');

        return [$config, $serverDependencies, $cacheable];
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
