<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Annotation;

use PeibinLaravel\Di\Contracts\Annotation;
use PeibinLaravel\Di\Exception\DirectoryNotExistException;
use ReflectionClass;

class Scanner
{
    /** @var ScanConfig */
    private $scanConfig;

    public function __construct(ScanConfig $scanConfig)
    {
        $this->scanConfig = $scanConfig;

        foreach ($scanConfig->getIgnoreAnnotations() as $annotation) {
            AnnotationReader::addGlobalIgnoredName($annotation);
        }
    }

    /**
     * @return array|void
     * @throws DirectoryNotExistException
     */
    public function scan()
    {
        $paths = $this->scanConfig->getPaths();
        $collectors = $this->scanConfig->getCollectors();

        if (!$paths) {
            return [];
        }

        $annotationReader = new AnnotationReader();

        $paths = $this->normalizeDir($paths);

        $classes = ReflectionManager::getAllClasses($paths);

        foreach ($classes as $className => $reflectionClass) {
            /** @var MetadataCollector $collector */
            foreach ($collectors as $collector) {
                $collector::clear($className);
            }

            $this->collect($annotationReader, $reflectionClass);
        }
    }

    public function collect(AnnotationReader $reader, ReflectionClass $reflection)
    {
        $className = $reflection->getName();
        if ($path = $this->scanConfig->getClassMap()[$className] ?? null) {
            if ($reflection->getFileName() !== $path) {
                // When the original class is dynamically replaced, the original class should not be collected.
                return;
            }
        }
        // Parse class annotations
        $classAnnotations = $reader->getClassAnnotations($reflection);
        if (!empty($classAnnotations)) {
            foreach ($classAnnotations as $classAnnotation) {
                if ($classAnnotation instanceof Annotation) {
                    $classAnnotation->collectClass($className);
                }
            }
        }
        // Parse properties annotations
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $propertyAnnotations = $reader->getPropertyAnnotations($property);
            if (!empty($propertyAnnotations)) {
                foreach ($propertyAnnotations as $propertyAnnotation) {
                    if ($propertyAnnotation instanceof Annotation) {
                        $propertyAnnotation->collectProperty($className, $property->getName());
                    }
                }
            }
        }
        // Parse methods annotations
        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            $methodAnnotations = $reader->getMethodAnnotations($method);
            if (!empty($methodAnnotations)) {
                foreach ($methodAnnotations as $methodAnnotation) {
                    if ($methodAnnotation instanceof Annotation) {
                        $methodAnnotation->collectMethod($className, $method->getName());
                    }
                }
            }
        }

        unset($reflection, $classAnnotations, $properties, $methods);
    }

    /**
     * Normalizes given directory names by removing directory not exist.
     * @throws DirectoryNotExistException
     */
    public function normalizeDir(array $paths): array
    {
        $result = [];
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $result[] = $path;
            }
        }

        if ($paths && !$result) {
            throw new DirectoryNotExistException('The scanned directory does not exist');
        }

        return $result;
    }
}
