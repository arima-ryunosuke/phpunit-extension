<?php

namespace ryunosuke\PHPUnit;

use Flow\JSONPath\JSONPath;
use JmesPath\Env;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringStartsWith;
use ryunosuke\PHPUnit\Constraint\IsCType;
use ryunosuke\PHPUnit\Constraint\IsThrowable;
use ryunosuke\PHPUnit\Constraint\IsValid;
use ryunosuke\PHPUnit\Constraint\LogicalAnd;
use ryunosuke\PHPUnit\Constraint\LogicalNot;
use ryunosuke\PHPUnit\Constraint\LogicalOr;
use Symfony\Component\CssSelector\CssSelectorConverter;

if (!trait_exists(Annotation::class)) {
    trait Annotation
    {
    }
}

class Actual implements \ArrayAccess
{
    use Annotation;

    public static $compatibleVersion = "2.0.0";

    public static $constraintVariations = [
        // alias
        'is'                   => IsEqual::class,
        'isSame'               => IsIdentical::class,
        'prefixIs'             => StringStartsWith::class,
        'suffixIs'             => StringEndsWith::class,
        'equalsCanonicalizing' => [IsEqual::class => ['canonicalize' => true]],
        'equalsIgnoreCase'     => [IsEqual::class => [4 => true]],
        'matches'              => RegularExpression::class,
        'gt'                   => GreaterThan::class,
        'lt'                   => LessThan::class,
        'gte'                  => [IsEqual::class, GreaterThan::class],
        'lte'                  => [IsEqual::class, LessThan::class],
        'isNullOrString'       => [IsNull::class, IsType::class => [IsType::TYPE_STRING]],
        'wasThrown'            => IsThrowable::class,
        // via IsType
        'isArray'              => [IsType::class => [IsType::TYPE_ARRAY]],
        'isBool'               => [IsType::class => [IsType::TYPE_BOOL]],
        'isFloat'              => [IsType::class => [IsType::TYPE_FLOAT]],
        'isInt'                => [IsType::class => [IsType::TYPE_INT]],
        # 'isNull'             => [IsType::class => [IsType::TYPE_NULL]], // already exists internal
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
        'isValidInt'           => [IsValid::class => [IsValid::VALID_INT]],
        'isValidFloat'         => [IsValid::class => [IsValid::VALID_FLOAT]],
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

    private static $object = [];

    /** @var mixed testing value */
    private $actual;

    /** @var Actual */
    private $parent;

    /** @var array */
    private $arguments = [];

    /** @var string */
    private $message = '';

    private static function compareVersion(string $target): int
    {
        $version = implode('.', explode('.', (string) self::$compatibleVersion) + [1 => '0', 2 => '0']);
        return version_compare($version, $target);
    }

    public static function generateAnnotation($types = [])
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
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    $tname = $parameter->getType()->getName();
                    $tname = (class_exists($tname) ? '\\' : '') . $tname;
                    $type = ($parameter->allowsNull() ? '?' : '') . $tname . ' ';
                }
                $arg = $type . '$' . $parameter->getName();
                if (array_key_exists($parameter->getPosition(), $defaults)) {
                    $arg .= ' = ' . var_export($defaults[$parameter->getPosition()], true);
                }
                elseif (array_key_exists($parameter->getName(), $defaults)) {
                    $arg .= ' = ' . var_export($defaults[$parameter->getName()], true);
                }
                elseif ($parameter->isDefaultValueAvailable()) {
                    $arg .= ' = ' . var_export($parameter->getDefaultValue(), true);
                }
                return $arg;
            }, $parameters), function ($v) { return $v !== null; });
            $argstring = implode(', ', $argstrings);

            $eachName = "each" . ucfirst($mname);
            $result[$eachName] = "$returnType $eachName($argstring)";

            $result[$mname] = "$returnType $mname($argstring)";

            $notName = lcfirst(LogicalNot::export($mname));
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

                $anyName = lcfirst(LogicalOr::export($mname));
                $allName = lcfirst(LogicalAnd::export($mname));
                $notAnyName = lcfirst(LogicalOr::export($notName));
                $notAllName = lcfirst(LogicalAnd::export($notName));
                $result[$anyName] = "$returnType $anyName($argstring)";
                $result[$allName] = "$returnType $allName($argstring)";
                $result[$notAnyName] = "$returnType $notAnyName($argstring)";
                $result[$notAllName] = "$returnType $notAllName($argstring)";
            }

            return $result;
        };

        $dummyConstructor = (new \ReflectionClass(new class() {
            public function __construct() { }
        }))->getConstructor();

        $annotations = [];

        $types += [
            'constraint' => true,
            'variation'  => true,
            'function'   => false,
        ];

        if ($types['constraint']) {
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
        }

        if ($types['variation']) {
            foreach (self::$constraintVariations as $name => $variation) {
                if ($variation instanceof \Closure) {
                    $method = new \ReflectionFunction($variation);

                    $via = strtr(Util::reflectFile($method), ['\\' => '/']);
                    $parameters = array_slice($method->getParameters(), 1);
                    $annotations = array_merge($annotations, [$via => $annotate($name, $parameters, [])]);
                    continue;
                }

                if ($variation instanceof Constraint) {
                    $refclass = new \ReflectionClass($variation);
                    $method = $refclass->getConstructor() ?? $dummyConstructor;

                    $via = strtr(Util::reflectFile($refclass), ['\\' => '/']);
                    $parameters = $method->getParameters();
                    $annotations = array_merge($annotations, [$via => $annotate($name, $parameters, get_object_properties($variation))]);
                    continue;
                }

                $via = $parameters = $defaults = [];
                foreach ((array) $variation as $classname => $defaults) {
                    if (is_int($classname)) {
                        $classname = $defaults;
                        $defaults = [];
                    }
                    $refclass = new \ReflectionClass($classname);
                    $method = $refclass->getConstructor() ?? $dummyConstructor;

                    $via[] = "\\{$refclass->name}::{$method->name}()" . ($defaults ? ' ' . json_encode($defaults) : '');
                    $parameters = $method->getParameters();
                }

                $annotations = array_merge($annotations, [implode(',', $via) => $annotate($name, $parameters, $defaults)]);
            }
        }

        if ($types['function']) {
            foreach (get_defined_functions(true) as $type => $functions) {
                foreach ($functions as $funcname) {
                    $reffunc = new \ReflectionFunction($funcname);

                    if (!in_array((string) $reffunc->getExtensionName(), ['Core', 'date', 'hash', 'pcre', 'standard', 'mbstring', ''])) {
                        continue;
                    }
                    if ($reffunc->isUserDefined() && stripos($funcname, __NAMESPACE__) === false) {
                        continue;
                    }
                    if ($reffunc->getNumberOfParameters() === 0) {
                        continue;
                    }
                    foreach ($reffunc->getParameters() as $p) {
                        if ($p->isPassedByReference()) {
                            continue 2;
                        }
                    }

                    $variation = [];
                    $parameters = $reffunc->getParameters();
                    $paramargs = function_parameter($reffunc);
                    foreach (range(0, $reffunc->getNumberOfParameters() - 1) as $n) {
                        $params = $paramargs;
                        unset($params['$' . $parameters[$n]->getName()]);
                        $variation[] = '\\' . __CLASS__ . ' ' . $reffunc->getShortName() . ($n ? $n : '') . "(" . implode(', ', $params) . ")";
                    }

                    $via = $reffunc->isInternal()
                        ? "https://www.php.net/manual/function." . strtr($reffunc->getShortName(), ['_' => '-']) . ".php"
                        : strtr(Util::reflectFile($reffunc), ['\\' => '/']);
                    $annotations = array_merge($annotations, [$via => $variation]);
                }
            }
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

    private function create($actual, $arguments = []): Actual
    {
        $that = new static($actual);
        $that->parent = $this;
        $that->arguments = $arguments;
        return $that;
    }

    public static function __callStatic($name, $arguments)
    {
        return new static((static::$object)::$name(...$arguments));
    }

    public function __construct($actual)
    {
        $this->actual = $actual;
        $this->parent = $this;

        if (is_object($actual)) {
            static::$object = get_class($actual);
        }
        elseif (@class_exists($actual)) {
            static::$object = (string) $actual;
        }
    }

    public function __toString()
    {
        if (is_object($this->actual)) {
            $staticCaller = new class(static::class, $this->actual) {
                public static $static, $class;

                public function __construct($static, $object)
                {
                    self::$static = $static;
                    self::$class = get_class($object);
                }

                public static function __callStatic($name, $arguments)
                {
                    $static = self::$static;
                    return new $static((self::$class)::$name(...$arguments));
                }
            };
            return get_class($staticCaller);
        }
        return var_export2($this->actual, true) . "\n";
    }

    public function __call($name, $arguments)
    {
        $callee = $name;
        $modes = [];

        $callee = preg_replace('#^each#', '', $callee, 1, $count);
        $modes['each'] = !!$count;

        $callee = LogicalNot::import($callee2 = $callee);
        $modes['not'] = $callee2 !== $callee;

        $callee = LogicalOr::import($callee2 = $callee);
        $modes['any'] = $callee2 !== $callee;

        $callee = LogicalAnd::import($callee2 = $callee);
        $modes['all'] = $callee2 !== $callee;

        $actuals = $modes['each'] ? $this->actual : [$this->actual];

        $callee = lcfirst($callee);
        if (isset(self::$constraintVariations[$callee])) {
            $variation = self::$constraintVariations[$callee];
            if ($variation instanceof \Closure) {
                $constraint = new class($variation, $arguments) extends Constraint {
                    private $callback;
                    private $arguments;

                    public function __construct(callable $callback, array $arguments)
                    {
                        $this->callback = $callback;
                        $this->arguments = $arguments;
                    }

                    public function toString(): string
                    {
                        $args = [];
                        $ref = new \ReflectionFunction($this->callback);
                        foreach (array_slice($ref->getParameters(), 1) as $n => $p) {
                            $args[$p->getName()] = array_key_exists($n, $this->arguments) ? $this->arguments[$n] : $p->getDefaultValue();
                        }
                        array_walk_recursive($args, function (&$v) {
                            if (is_string($v)) {
                                $v = strtr($v, ["\r\n" => '\r\n', "\r" => '\r', "\n" => '\n']);
                            }
                        });
                        return sprintf('is accepted by function (%s) %s', paml_export($args), callable_code($this->callback)[1]);
                    }

                    protected function matches($other): bool
                    {
                        return ($this->callback)($other, ...$this->arguments);
                    }
                };
                return $this->assert($actuals, $constraint);
            }
            if ($variation instanceof Constraint) {
                $constraint = clone $variation;
                if ($arguments) {
                    $refclass = new \ReflectionClass($constraint);
                    $refclass->getConstructor()->invokeArgs($constraint, $arguments);
                }
                return $this->assert($actuals, $constraint);
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

        if ($this->functionArgument($name) !== null) {
            return $this->function($name, ...$arguments);
        }

        return $this->try($name, ...$arguments);
    }

    public function __invoke(...$arguments): Actual
    {
        if (!($this->actual instanceof \Closure || (!is_object($this->actual) && is_callable($this->actual)))) {
            array_unshift($arguments, null);
        }

        return $this->try(...$arguments);
    }

    public function __get($name): Actual
    {
        if ($name === 'and') {
            return $this->and();
        }

        if ($name[0] === '$') {
            return $this->create((new JSONPath($this->actual))->find($name)->data());
        }

        // for convenience
        $actual = Util::stringToStructure($this->actual);
        if (is_array($actual)) {
            return $this->create($actual[$name]);
        }
        return $this->create(Util::propertyToValue($actual, $name));
    }

    public function __set($name, $value)
    {
        $this->actual->$name = $value;
    }

    public function __unset($name)
    {
        unset($this->actual->$name);
    }

    public function offsetGet($offset): Actual
    {
        $actual = Util::stringToStructure($this->actual);

        if ($actual instanceof \SimpleXMLElement) {
            try {
                if (isset($actual[$offset])) {
                    throw new \Symfony\Component\CssSelector\Exception\SyntaxErrorException();
                }
                if ($offset[0] !== '/') {
                    $offset = (new CssSelectorConverter(true))->toXPath($offset);
                }
                $value = @$actual->xpath($offset);
                if ($value === false) {
                    throw new \Symfony\Component\CssSelector\Exception\SyntaxErrorException();
                }
            }
            catch (\Symfony\Component\CssSelector\Exception\SyntaxErrorException $e) {
                $value = $actual[$offset];
            }
        }
        elseif (is_array($actual) || $actual instanceof \ArrayAccess || $actual instanceof \stdClass) {
            try {
                $value = Env::search($offset, $actual);
            }
            catch (\JmesPath\SyntaxErrorException $e) {
                $value = $actual[$offset];
            }
        }
        elseif (is_string($actual)) {
            $value = preg_matches($offset, $actual, PREG_SET_ORDER);
        }
        else {
            throw new \DomainException('$this->actual must be structure value given ' . gettype($actual) . ').');
        }

        return $this->create($value);
    }

    public function offsetSet($offset, $value)
    {
        $this->actual[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->actual[$offset]);
    }

    public function var(string $propertyname)
    {
        return Util::propertyToValue($this->actual, $propertyname);
    }

    public function use(string $methodname): \Closure
    {
        return \Closure::fromCallable(Util::methodToCallable($this->actual, $methodname));
    }

    public function callable(string $methodname, ...$bindings): Actual
    {
        $method = Util::methodToCallable($this->actual, $methodname);
        if ($bindings) {
            $method = array_merge([$method], $bindings);
        }
        return $this->create($method);
    }

    public function do($name, ...$arguments): Actual
    {
        if ($this->actual instanceof \Closure || !is_object($this->actual) && is_callable($this->actual)) {
            if (func_num_args()) {
                array_unshift($arguments, $name);
            }
            return $this->create(($this->actual)(...$arguments), $arguments);
        }

        return $this->create((Util::methodToCallable($this->actual, $name))(...$arguments), $arguments);
    }

    public function try($method = null, ...$arguments): Actual
    {
        if ($this->actual instanceof \Closure || (!is_object($this->actual) && is_callable($this->actual))) {
            if (func_num_args()) {
                array_unshift($arguments, $method);
            }
            $callee = $this->actual;
        }
        else {
            $callee = Util::methodToCallable($this->actual, $method);
        }

        try {
            $return = $callee(...$arguments);
        }
        catch (\Throwable $t) {
            $return = $t;
        }
        return $this->create($return, $arguments);
    }

    public function list($index = null): Actual
    {
        if ($index === null) {
            return $this->create($this->arguments);
        }
        return $this->create($this->arguments[$index]);
    }

    /**
     * @param callable $function
     * @param mixed ...$arguments
     * @return Actual
     */
    public function function ($function, ...$arguments): Actual
    {
        return $this->create(chain($this->actual)->$function(...$arguments)(), $arguments);
    }

    /**
     * @param callable $function
     * @param mixed ...$arguments
     * @return Actual
     */
    public function foreach($function, ...$arguments): Actual
    {
        $methodMode = is_string($function) && (strpos($function, '->') === 0 || strpos($function, '::') === 0);
        $function = $this->functionArgument($function);

        $actuals = [];
        foreach ($this->actual as $k => $actual) {
            if ($methodMode) {
                $method = Util::methodToCallable($actual, $function);
                $actuals[$k] = $method(...$arguments);
            }
            else {
                $actuals[$k] = chain($actual)->$function(...$arguments)();
            }
        }
        return $this->create($actuals, $arguments);
    }

    public function return()
    {
        return $this->actual;
    }

    public function eval(Constraint ...$constraints): Actual
    {
        return $this->assert([$this->actual], ...$constraints);
    }

    public function as(string $message): Actual
    {
        $this->message = $message;
        return $this;
    }

    public function and(): Actual
    {
        return $this;
    }

    public function exit(int $nest = 1): Actual
    {
        $that = $this;
        do {
            $that = $that->parent;
        } while (--$nest > 0);
        return $that;
    }

    private function assert($actuals, Constraint ...$constraints): Actual
    {
        $constraint = LogicalOr::fromConstraints(...$constraints);
        foreach ($actuals as $actual) {
            Assert::assertThat($actual, $constraint, $this->message);
        }
        return $this;
    }

    private function functionArgument($function): ?string
    {
        foreach ([$function, __NAMESPACE__ . "\\$function"] as $fname) {
            if (is_callable($fname)) {
                return $fname;
            }

            if (preg_match('#(.+?)(\d)$#', $fname, $match) && is_callable($match[1])) {
                return $fname;
            }

            if (strpos($fname, '->') === 0 || strpos($fname, '::') === 0) {
                return substr($fname, 2);
            }
        }

        return null;
    }

    private function newConstraint(string $constraintClass, array $arguments, array $modes): Constraint
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

    public function __isset($name) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetExists($offset) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }
}
