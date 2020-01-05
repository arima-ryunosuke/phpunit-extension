<?php

namespace ryunosuke\PHPUnit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\RegularExpression;
use ryunosuke\PHPUnit\Constraint\IsCType;
use ryunosuke\PHPUnit\Constraint\IsValid;
use ryunosuke\PHPUnit\Constraint\LogicalAnd;
use ryunosuke\PHPUnit\Constraint\LogicalNot;
use ryunosuke\PHPUnit\Constraint\LogicalOr;
use ryunosuke\PHPUnit\Constraint\OutputMatches;
use ryunosuke\PHPUnit\Constraint\Throws;

if (!trait_exists(Annotation::class)) {
    trait Annotation
    {
    }
}

class Actual implements \ArrayAccess
{
    use Annotation;

    public static $constraintVariations = [
        // alias
        'is'                   => IsEqual::class,
        'isSame'               => IsIdentical::class,
        'equalsCanonicalizing' => [IsEqual::class => ['canonicalize' => true]],
        'equalsIgnoreCase'     => [IsEqual::class => [4 => true]],
        'matches'              => RegularExpression::class,
        'gt'                   => GreaterThan::class,
        'lt'                   => LessThan::class,
        'gte'                  => [IsEqual::class, GreaterThan::class],
        'lte'                  => [IsEqual::class, LessThan::class],
        'isNullOrString'       => [IsNull::class, IsType::class => [IsType::TYPE_STRING]],
        // via IsType
        'isArray'              => [IsType::class => [IsType::TYPE_ARRAY]],
        'isBool'               => [IsType::class => [IsType::TYPE_BOOL]],
        'isFloat'              => [IsType::class => [IsType::TYPE_FLOAT]],
        'isInt'                => [IsType::class => [IsType::TYPE_INT]],
        # 'isNull'        => [IsType::class => [IsType::TYPE_NULL]], // already exists internal
        'isNumeric'            => [IsType::class => [IsType::TYPE_NUMERIC]],
        'isObject'             => [IsType::class => [IsType::TYPE_OBJECT]],
        'isResource'           => [IsType::class => [IsType::TYPE_RESOURCE]],
        'isString'             => [IsType::class => [IsType::TYPE_STRING]],
        'isScalar'             => [IsType::class => [IsType::TYPE_SCALAR]],
        'isCallable'           => [IsType::class => [IsType::TYPE_CALLABLE]],
        'isIterable'           => [IsType::class => [IsType::TYPE_ITERABLE]],
        // via IsCType
        'isCtypeAlnum'         => [IsCType::class => [IsCType::CTYPE_ALNUM]],
        'isCtypeAlpha'         => [IsCType::class => [IsCType::CTYPE_ALPHA]],
        'isCtypeCntrl'         => [IsCType::class => [IsCType::CTYPE_CNTRL]],
        'isCtypeDigit'         => [IsCType::class => [IsCType::CTYPE_DIGIT]],
        'isCtypeGraph'         => [IsCType::class => [IsCType::CTYPE_GRAPH]],
        'isCtypeLower'         => [IsCType::class => [IsCType::CTYPE_LOWER]],
        'isCtypePrint'         => [IsCType::class => [IsCType::CTYPE_PRINT]],
        'isCtypePunct'         => [IsCType::class => [IsCType::CTYPE_PUNCT]],
        'isCtypeSpace'         => [IsCType::class => [IsCType::CTYPE_SPACE]],
        'isCtypeUpper'         => [IsCType::class => [IsCType::CTYPE_UPPER]],
        'isCtypeXdigit'        => [IsCType::class => [IsCType::CTYPE_XDIGIT]],
        // via IsValid
        'isValidEmail'         => [IsValid::class => [IsValid::VALID_EMAIL]],
        'isValidIp'            => [IsValid::class => [IsValid::VALID_IP]],
        'isValidIpv4'          => [IsValid::class => [IsValid::VALID_IPV4]],
        'isValidIpv6'          => [IsValid::class => [IsValid::VALID_IPV6]],
        'isValidMac'           => [IsValid::class => [IsValid::VALID_MAC]],
        'isValidUrl'           => [IsValid::class => [IsValid::VALID_URL]],
    ];

    public static $constraintNamespaces = [
        "\\ryunosuke\\PHPUnit\\Constraint\\" => __DIR__ . '/Constraint',
        "\\PHPUnit\\Framework\\Constraint\\" => __DIR__ . '/../vendor/phpunit/phpunit/src/Framework/Constraint',
    ];

    /** @var mixed testing value */
    private $actual;

    /** @var Actual */
    private $parent;

    /** @var bool */
    private $autoback;

    /** @var Throws */
    private $catch;

    /** @var OutputMatches */
    private $output;

    /** @var string */
    private $message = '';

    public static function generateAnnotation(bool $rawarray = false)
    {
        $annotate = function ($mname, $parameters, $defaults) {
            $result = [];
            $returnType = '\\' . __CLASS__;

            $argstrings = array_filter(array_map(function (\ReflectionParameter $parameter) use ($defaults) {
                if (!$parameter->isOptional() && array_key_exists($parameter->getPosition(), $defaults)) {
                    return null;
                }
                $type = '';
                if ($parameter->hasType()) {
                    $tname = $parameter->getType()->getName();
                    $tname = (class_exists($tname) ? '\\' : '') . $tname;
                    $type = ($parameter->allowsNull() ? '?' : '') . $tname . ' ';
                }
                $arg = $type . '$' . $parameter->getName();
                if (array_key_exists($parameter->getPosition(), $defaults)) {
                    $arg .= ' = ' . var_export($defaults[$parameter->getPosition()], true);
                }
                elseif ($parameter->isDefaultValueAvailable()) {
                    $arg .= ' = ' . var_export($parameter->getDefaultValue(), true);
                }
                return $arg;
            }, $parameters), function ($v) { return $v !== null; });
            $argstring = implode(', ', $argstrings);

            // $allName = preg_replace('#^is([A-Z]+)#', 'allAre$1', $mname, 1, $count);
            $allName = "all" . ucfirst($mname);
            $result[$allName] = "$returnType $allName($argstring)";

            $result[$mname] = "$returnType $mname($argstring)";

            $notName = preg_replace('#^notIs#', 'isNot', "not" . ucfirst($mname), 1);
            $result[$notName] = "$returnType $notName($argstring)";

            $requiredCount = array_reduce($parameters, function ($carry, \ReflectionParameter $parameter) use ($defaults) {
                return $carry + (int) !($parameter->isOptional() || array_key_exists($parameter->getPosition(), $defaults));
            }, 0);
            if ($requiredCount) {
                $requiredParams = array_slice($parameters, 0, $requiredCount);
                $optionalParams = array_slice($argstrings, $requiredCount);
                $firstParam = implode('', array_map(function (\ReflectionParameter $parameter) {
                    return $parameter->getName();
                }, $requiredParams));
                $argstring = implode(', ', array_merge(["array \${$firstParam}s"], $optionalParams));

                $anyName = $mname . 'Any';
                $result[$anyName] = "$returnType $anyName($argstring)";
                $allName = $mname . 'All';
                $result[$allName] = "$returnType $allName($argstring)";
            }

            return $result;
        };

        $dummyConstructor = (new \ReflectionClass(new class()
        {
            public function __construct() { }
        }))->getConstructor();

        $annotations = [];

        foreach (self::$constraintNamespaces as $namespace => $directory) {
            foreach (glob("$directory/*.php") as $file) {
                $refclass = new \ReflectionClass('\\' . trim($namespace, '\\') . '\\' . basename($file, '.php'));
                if (false
                    || $refclass->isAbstract()
                    || strpos($refclass->getShortName(), 'Logical') === 0
                    || !is_subclass_of($refclass->name, Constraint::class)
                ) {
                    continue;
                }
                $method = $refclass->getConstructor() ?? $dummyConstructor;

                $via = "\\{$refclass->name}";
                $name = lcfirst($refclass->getShortName());
                $parameters = $method->getParameters();

                $annotations = array_merge($annotations, [$via => $annotate($name, $parameters, [])]);
            }
        }

        foreach (self::$constraintVariations as $name => $variation) {
            if ($variation instanceof Constraint) {
                $refclass = new \ReflectionClass($variation);
                $method = $refclass->getConstructor() ?? $dummyConstructor;

                $via = sprintf('%s#%d-%d', basename($refclass->getFileName()), $refclass->getStartLine(), $refclass->getEndLine());
                $parameters = $method->getParameters();
                $annotations = array_merge($annotations, [$via => $annotate($name, $parameters, [])]);
                continue;
            }

            $via = $parameters = $defaults = [];
            foreach ((array) $variation as $classname => $args) {
                if (is_int($classname)) {
                    $classname = $args;
                    $args = [];
                }
                $refclass = new \ReflectionClass($classname);
                $method = $refclass->getConstructor() ?? $dummyConstructor;

                $via[] = "\\{$refclass->name}::{$method->name}()" . ($args ? ' ' . json_encode($args) : '');
                $parameters = $method->getParameters();
                $defaults = array_reduce($parameters, function ($carry, \ReflectionParameter $parameter) use ($args) {
                    if (array_key_exists($parameter->getName(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getName()];
                    }
                    elseif (array_key_exists($parameter->getPosition(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getPosition()];
                    }
                    return $carry;
                }, []);
            }

            $annotations = array_merge($annotations, [implode(',', $via) => $annotate($name, $parameters, $defaults)]);
        }

        if ($rawarray) {
            return $annotations;
        }

        $result = [];
        foreach ($annotations as $name => $methods) {
            $result[] = ' * @see ' . $name;
            foreach ($methods as $method) {
                $result[] = ' * @method ' . $method;
            }
            $result[] = ' *';
        }
        return "/**\n" . implode("\n", $result) . "\n */";
    }

    private function create($actual): Actual
    {
        $that = new static($actual);
        $that->parent = $this;
        $that->autoback = !!$this->autoback;
        return $that;
    }

    public function __construct($actual, bool $autoback = false)
    {
        $this->actual = $actual;
        $this->parent = $this;
        $this->autoback = $autoback;
    }

    public function __invoke(...$arguments): Actual
    {
        return $this->invoke($this->actual, $arguments);
    }

    public function __call($name, $arguments)
    {
        $callee = $name;
        $modes = [];

        $callee = preg_replace('#^all#', '', $callee, 1, $count);
        $modes['every'] = !!$count;

        $callee = preg_replace('#^(is)?(Not|not)([A-Z])#', '$1$3', $callee, 1, $count);
        $modes['not'] = !!$count;

        $callee = preg_replace('#Any$#', '', $callee, 1, $count);
        $modes['any'] = !!$count;

        $callee = preg_replace('#All$#', '', $callee, 1, $count);
        $modes['all'] = !!$count;

        $actuals = $modes['every'] ? $this->actual : [$this->actual];

        $callee = lcfirst($callee);
        if (isset(self::$constraintVariations[$callee])) {
            $variation = self::$constraintVariations[$callee];
            if ($variation instanceof Constraint) {
                $refclass = new \ReflectionClass($variation);
                $constructor = $refclass->getConstructor();
                if ($constructor) {
                    $constructor->invokeArgs($variation, $arguments);
                }
                return $this->assert($actuals, $variation);
            }

            $constraints = [];
            foreach ((array) $variation as $classname => $args) {
                if (is_int($classname)) {
                    $classname = $args;
                    $args = [];
                }
                $constraints[] = $this->newConstraint($classname, $arguments + $args, $modes);
            }
            return $this->assert($actuals, ...$constraints);
        }

        $callee = ucfirst($callee);
        foreach (self::$constraintNamespaces as $namespace => $directory) {
            if (class_exists($constraintClass = trim($namespace, '\\') . '\\' . $callee)) {
                $constraint = $this->newConstraint($constraintClass, $arguments, $modes);
                return $this->assert($actuals, $constraint);
            }
        }

        if ($invoker = $this->invokerToCallable($this->actual, $name)) {
            return $this->invoke($invoker, $arguments);
        }

        throw new \BadMethodCallException("$name -> $callee");
    }

    public function __get($name): Actual
    {
        return $this->create($this->getProperty($this->actual, $name));
    }

    public function offsetGet($offset): Actual
    {
        Assert::assertArrayHasKey($offset, $this->actual);
        return $this->create($this->actual[$offset]);
    }

    public function do($name, ...$arguments): Actual
    {
        return $this->invoke($this->invokerToCallable($this->actual, $name), $arguments);
    }

    public function exit(int $nest = 1): Actual
    {
        $that = $this;
        do {
            $that = $that->parent;
        } while (--$nest > 0);
        return $that;
    }

    public function var(string $propertyname)
    {
        return $this->getProperty($this->actual, $propertyname);
    }

    public function use(string $methodname): \Closure
    {
        return \Closure::fromCallable($this->invokerToCallable($this->actual, $methodname));
    }

    public function try($method = null, ...$arguments): Actual
    {
        $callee = $this->invokerToCallable($this->actual, $method);
        try {
            $return = $callee(...$arguments);
        }
        catch (\Throwable $t) {
            $return = $t;
        }
        return $this->create($return);
    }

    public function catch(...$expected): Actual
    {
        $this->catch = new Throws(...$expected);
        return $this;
    }

    public function print($expected): Actual
    {
        $this->output = new OutputMatches($expected);
        return $this;
    }

    public function as(string $message): Actual
    {
        $this->message = $message;
        return $this;
    }

    public function return()
    {
        return $this->actual;
    }

    public function eval(Constraint ...$constraints): Actual
    {
        return $this->assert([$this->actual], ...$constraints);
    }

    private function assert($actuals, Constraint ...$constraints): Actual
    {
        $constraint = LogicalOr::fromConstraints(...$constraints);
        foreach ($actuals as $actual) {
            Assert::assertThat($actual, $constraint, $this->message);
        }
        if ($this->autoback) {
            return $this->exit();
        }
        return $this;
    }

    private function newConstraint($constraintClass, $arguments, $modes): Constraint
    {
        $newConstraint = function ($args) use ($constraintClass, $modes) {
            $constructor = (new \ReflectionClass($constraintClass))->getConstructor();
            if ($constructor) {
                $args = array_reduce($constructor->getParameters(), function ($carry, \ReflectionParameter $parameter) use ($args) {
                    if (array_key_exists($parameter->getName(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getName()];
                    }
                    elseif (array_key_exists($parameter->getPosition(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getPosition()];
                    }
                    elseif ($parameter->isDefaultValueAvailable()) {
                        $carry[$parameter->getPosition()] = $parameter->getDefaultValue();
                    }
                    return $carry;
                }, []);
            }
            $constraint = new $constraintClass(...$args);
            if ($modes['not']) {
                $constraint = new LogicalNot($constraint);
            }
            return $constraint;
        };

        if ($modes['any'] || $modes['all']) {
            $constraints = [];
            $values = reset($arguments);
            foreach (is_array($values) ? $values : [$values] as $value) {
                $constraints[] = $newConstraint([$value] + $arguments);
            }
            if ($modes['any']) {
                return LogicalOr::fromConstraints(...$constraints);
            }
            if ($modes['all']) {
                return LogicalAnd::fromConstraints(...$constraints);
            }
        }
        return $newConstraint($arguments);
    }

    private function invoke($callee, $arguments): Actual
    {
        if ($this->catch) {
            $catch = $this->catch;
            $this->catch = null;
            return $this->assert([array_merge([$callee], $arguments)], $catch);
        }
        if ($this->output) {
            $output = $this->output;
            $this->output = null;
            return $this->assert([array_merge([$callee], $arguments)], $output);
        }
        return $this->create($callee(...$arguments));
    }

    private function getProperty($object, string $property)
    {
        $refclass = new \ReflectionObject($object);
        do {
            if ($refclass->hasProperty($property)) {
                $refproperty = $refclass->getProperty($property);
                $refproperty->setAccessible(true);
                return $refproperty->isStatic() ? $refproperty->getValue() : $refproperty->getValue($object);
            }
        } while ($refclass = $refclass->getParentClass());

        if (method_exists($object, '__get')) {
            return $object->$property;
        }
        return Assert::assertObjectHasAttribute($property, $object);
    }

    private function invokerToCallable($object, string $method = null): ?callable
    {
        if (!is_object($object)) {
            return null;
        }
        if ($method === null && method_exists($object, '__invoke')) {
            return $object;
        }
        if (is_callable([$object, $method])) {
            if (method_exists($object, $method)) {
                return [$object, $method];
            }
            // treat __call magic method via @method
            $ref = new \ReflectionClass($object);
            do {
                $doccomment = $ref->getDocComment();
                preg_match_all('#@method\s+([_0-9a-z\\\\|\[\]$]+\s+)?\s*?([_0-9a-z]+)\(#iums', $doccomment, $matches, PREG_SET_ORDER);
                foreach ($matches as [, , $mname]) {
                    if (strcasecmp($method, $mname) === 0) {
                        return [$object, $method];
                    }
                }
            } while ($ref = $ref->getParentClass());
        }
        $refmethod = (new \ReflectionMethod($object, $method));
        return $refmethod->isStatic() ? $refmethod->getClosure() : $refmethod->getClosure($object);
    }

    public function __isset($name) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function __unset($name) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function __set($name, $value) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetExists($offset) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetUnset($offset) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetSet($offset, $value) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }
}
