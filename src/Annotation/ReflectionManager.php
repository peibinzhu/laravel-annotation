<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Annotation;

use InvalidArgumentException;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Parser\Php7;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Finder\Finder;

class ReflectionManager extends MetadataCollector
{
    /** @var array */
    protected static $container = [];

    public static function reflectClass(string $className): ReflectionClass
    {
        if (!isset(static::$container['class'][$className])) {
            if (!class_exists($className) && !interface_exists($className) && !trait_exists($className)) {
                throw new InvalidArgumentException("Class {$className} not exist");
            }
            static::$container['class'][$className] = new ReflectionClass($className);
        }
        return static::$container['class'][$className];
    }

    public static function reflectMethod(string $className, string $method): ReflectionMethod
    {
        $key = $className . '::' . $method;
        if (!isset(static::$container['method'][$key])) {
            // TODO check interface_exist
            if (!class_exists($className)) {
                throw new InvalidArgumentException("Class {$className} not exist");
            }
            static::$container['method'][$key] = static::reflectClass($className)->getMethod($method);
        }
        return static::$container['method'][$key];
    }

    public static function reflectProperty(string $className, string $property): ReflectionProperty
    {
        $key = $className . '::' . $property;
        if (!isset(static::$container['property'][$key])) {
            if (!class_exists($className)) {
                throw new InvalidArgumentException("Class {$className} not exist");
            }
            static::$container['property'][$key] = static::reflectClass($className)->getProperty($property);
        }
        return static::$container['property'][$key];
    }

    public static function reflectPropertyNames(string $className)
    {
        $key = $className;
        if (!isset(static::$container['property_names'][$key])) {
            if (!class_exists($className) && !interface_exists($className) && !trait_exists($className)) {
                throw new InvalidArgumentException("Class {$className} not exist");
            }
            static::$container['property_names'][$key] = value(function () use ($className) {
                $properties = static::reflectClass($className)->getProperties();
                $result = [];
                foreach ($properties as $property) {
                    $result[] = $property->getName();
                }
                return $result;
            });
        }
        return static::$container['property_names'][$key];
    }

    public static function clear(?string $key = null): void
    {
        if ($key === null) {
            static::$container = [];
        }
    }

    public static function getPropertyDefaultValue(ReflectionProperty $property)
    {
        return method_exists($property, 'getDefaultValue')
            ? $property->getDefaultValue()
            : $property->getDeclaringClass()->getDefaultProperties()[$property->getName()] ?? null;
    }

    public static function getAllClasses(array $paths): array
    {
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');

        /** @var Php7 $parser */
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);

        $reflectionClasses = [];
        foreach ($finder as $file) {
            try {
                $stmts = $parser->parse($file->getContents());
                if (!$className = self::parseClassByStmts($stmts)) {
                    continue;
                }
                $reflectionClasses[$className] = static::reflectClass($className);
            } catch (\Throwable $e) {
            }
        }
        return $reflectionClasses;
    }

    public static function parseClassByStmts(array $stmts): string
    {
        $namespace = $className = '';
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Namespace_ && $stmt->name) {
                $namespace = $stmt->name->toString();
                foreach ($stmt->stmts as $node) {
                    if (($node instanceof Class_ || $node instanceof Interface_) && $node->name) {
                        $className = $node->name->toString();
                        break;
                    }
                }
            }
        }
        return ($namespace && $className) ? $namespace . '\\' . $className : '';
    }

    public static function getContainer(): array
    {
        return self::$container;
    }
}
