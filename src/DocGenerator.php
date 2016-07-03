<?php

namespace PhpSchool\Website;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionMethod;
use BetterReflection\Reflector\ClassReflector;
use BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use phpDocumentor\Reflection\DocBlock;
use BetterReflection\Reflection\ReflectionParameter;
use phpDocumentor\Reflection\DocBlock\Tag\ThrowsTag;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 *
 * Class DocGenerator
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DocGenerator
{
    public function generate() : array
    {
        $reflector = new ClassReflector(new AggregateSourceLocator(array_values(array_map(function (SplFileInfo $file) {
            return new SingleFileSourceLocator($file->getRealPath());
        }, iterator_to_array($this->getFiles())))));

        $namespaces = array_unique(array_map(function (ReflectionClass $class) {
            return $class->getNamespaceName();
        }, $reflector->getAllClasses()));

        $documentation = [
            'namespaces' => [],
        ];

        foreach ($namespaces as $namespace) {
            $classesInNamespace = array_filter(
                $reflector->getAllClasses(),
                function (ReflectionClass $class) use ($namespace) {
                    return $namespace === $class->getNamespaceName();
                }
            );

            $abstracts = array_filter(
                $classesInNamespace,
                function (ReflectionClass $class) {
                    return $class->isAbstract();
                }
            );

            $interfaces = array_filter(
                $classesInNamespace,
                function (ReflectionClass $class) {
                    return $class->isInterface();
                }
            );

            $traits = array_filter(
                $classesInNamespace,
                function (ReflectionClass $class) {
                    return $class->isTrait();
                }
            );

            $classes = array_udiff(
                $classesInNamespace,
                $abstracts,
                $interfaces,
                $traits,
                function (ReflectionClass $a, ReflectionClass $b) {
                    return $a === $b ? 0 : -1;
                }
            );

            $documentation['namespaces'][] = [
                'namespace'     => $namespace,
                'classes'       => $this->processClasses($classes),
                'interfaces'    => $this->processClasses($interfaces),
                'abstracts'     => $this->processClasses($abstracts),
                'traits'        => $this->processClasses($traits),
            ];
        }

        return $documentation;
    }

    private function getFiles() : Finder
    {
        return Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__ .'/../vendor/php-school/php-workshop/src')
            ->exclude('Command')
            ->exclude('Factory')
            ->exclude('Listener')
            ->exclude('MenuItem')
            ->exclude('NodeVisitor')
            ->notName('CommandRouter.php')
            ->notName('ExerciseRenderer.php')
            ->sortByName();
    }

    private function processClasses(array $classes) : array
    {
        return array_values(array_map(function (ReflectionClass $class) {
            return $this->processClass($class);
        }, $classes));
    }

    private function processClass(ReflectionClass $class) : array
    {
        $publicMethods = array_filter($class->getImmediateMethods(), function (ReflectionMethod $method) {
            return $method->isPublic();
        });

        $description = new DocBlock($class->getDocComment());

        return [
            'namespace'   => $class->getNamespaceName(),
            'name'        => $class->getShortName(),
            'description' => $description->getShortDescription() . "\n\n" . $description->getLongDescription(),
            'constants'   => array_map(function ($constName, $constValue) {
                return [
                    'name'  => $constName,
                    'value' => str_replace('\\\\', '\\', var_export($constValue, true)),
                ];
            }, array_keys($class->getConstants()), $class->getConstants()),
            'methods'   => array_map(function (ReflectionMethod $method) {
                return $this->processMethod($method);
            }, $publicMethods)
        ];
    }

    private function processMethod(ReflectionMethod $method) : array
    {
        $phpdoc = new DocBlock($method->getDocComment());
        $params = $phpdoc->getTagsByName('param');
        $throws = $phpdoc->getTagsByName('throws');

        $returnTypes = $method->getDocBlockReturnTypes();

        if (empty($returnTypes)) {
            $returnType = 'void';
        } else {
            $returnType = implode('|', $returnTypes);
        }

        $returnTypeTags = $phpdoc->getTagsByName('return');
        $returnTypeDescription = 'Does not return anything.';
        if (!empty($returnTypes)) {
            $returnTypeTag = array_shift($returnTypeTags);
            $returnTypeDescription = $returnTypeTag->getDescription();
        }

        return [
            'isStatic' => $method->isStatic(),
            'description' => $phpdoc->getShortDescription() . "\n\n" . $phpdoc->getLongDescription()->getContents(),
            'name' => $method->getName(),
            'params' => array_map(function (ReflectionParameter $parameter) use ($params) {
                return $this->processParam($parameter, $params);
            }, $method->getParameters()),
            'throws' => array_map(function (ThrowsTag $throw) {
                return [
                    'type' => ltrim($throw->getType(), '\\'),
                    'description' => $throw->getDescription(),
                ];
            }, $throws),
            'returnType' => ltrim($returnType, '\\'),
            'returnTypeDescription' => $returnTypeDescription,
        ];
    }

    private function processParam(ReflectionParameter $parameter, array $params) : array
    {
        $typeHint = $parameter->getTypeHint()
            ? $parameter->getTypeHint()->__toString()
            : implode('|', $parameter->getDocBlockTypeStrings());

        $description = null;
        foreach ($params as $paramTag) {
            /* @var $paramTag \phpDocumentor\Reflection\DocBlock\Tag\ParamTag */
            if ($paramTag->getVariableName() === '$' . $parameter->getName()) {
                if ($paramTag->getDescription() !== '') {
                    $description = $paramTag->getDescription();
                }
                break;
            }
        }

        $default = $parameter->isDefaultValueAvailable()
            ? $parameter->getDefaultValueAsString()
            : null;

        return [
            'typeHint'      => ltrim($typeHint, '\\'),
            'description'   => $description,
            'name'          => $parameter->getName(),
            'hasDefault'    => $parameter->isDefaultValueAvailable(),
            'default'       => $default
        ];
    }
}
