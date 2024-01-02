<?php

namespace ryunosuke\PHPUnit;

use ArrayAccess;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\RiskyTestError;
use PHPUnit\Framework\TestCase;
use ryunosuke\PHPUnit\Constraint\HtmlMatchesArray;
use ryunosuke\PHPUnit\Constraint\IsCType;
use ryunosuke\PHPUnit\Constraint\IsThrowable;
use ryunosuke\PHPUnit\Constraint\IsValid;
use ryunosuke\PHPUnit\Constraint\LogicalAnd;
use ryunosuke\PHPUnit\Constraint\LogicalNot;
use ryunosuke\PHPUnit\Constraint\LogicalOr;
use ryunosuke\PHPUnit\Constraint\OutputMatches;
use ryunosuke\PHPUnit\Exception\UndefinedException;

if (!trait_exists(Annotation::class)) { // @codeCoverageIgnore
    trait Annotation { }
}

class Actual implements \ArrayAccess
{
    use Annotation;

    public static $debugMode = false;

    public static $constraintVariations = [
        // alias
        'isSame'               => IsIdentical::class,
        'prefixIs'             => StringStartsWith::class,
        'suffixIs'             => StringEndsWith::class,
        'equalsCanonicalizing' => [IsEqual::class => ['canonicalize' => true]],
        'equalsIgnoreCase'     => [IsEqual::class => ['ignoreCase' => true]],
        'matches'              => RegularExpression::class,
        'gt'                   => GreaterThan::class,
        'lt'                   => LessThan::class,
        'gte'                  => [IsEqual::class, GreaterThan::class],
        'lte'                  => [IsEqual::class, LessThan::class],
        'isNullOrString'       => [IsNull::class, IsType::class => [IsType::TYPE_STRING]],
        'outputContains'       => [OutputMatches::class => ['raw' => true]],
        'outputEquals'         => [OutputMatches::class => ['raw' => true, 'with' => ['\\A', '\\z']]],
        'outputStartsWith'     => [OutputMatches::class => ['raw' => true, 'with' => ['\\A', '']]],
        'outputEndsWith'       => [OutputMatches::class => ['raw' => true, 'with' => ['', '\\z']]],
        'wasThrown'            => IsThrowable::class,
        'isUndefined'          => [IsInstanceOf::class => [UndefinedException::class]],
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
        'isValidDomain'        => [IsValid::class => [IsValid::VALID_DOMAIN]],
        'isValidHostname'      => [IsValid::class => [IsValid::VALID_HOSTNAME]],
    ];

    public static $constraintNamespaces = [
        "\\ryunosuke\\PHPUnit\\Constraint\\" => __DIR__ . '/Constraint',
        "\\PHPUnit\\Framework\\Constraint\\" => [
            __DIR__ . '/../../../phpunit/phpunit/src/Framework/Constraint',
            __DIR__ . '/../vendor/phpunit/phpunit/src/Framework/Constraint',
        ],
    ];

    /**
     * @deprecated
     */
    public static $functionNamespaces = [
        "\\"                   => ['*'],
        "\\ryunosuke\\PHPUnit" => ['*'],
    ];

    private static array $___objects = [];

    private $___actual;

    private Actual $___parent;

    private array $___arguments = [];

    private bool $___doneSomething = false;

    private string $___message = '';

    private bool $___breakable = false;

    private array $___results = [];

    private ?float $___elapsed = null;

    private ?string $___output         = null;
    private bool    $___outputAsserted = false;

    private ?Error $___error         = null;
    private bool   $___errorAsserted = false;

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
                    $tname = $parameter->getType()->getName();
                    $tname = (class_exists($tname) ? '\\' : '') . $tname;
                    $type = ($parameter->allowsNull() ? '?' : '') . $tname . ' ';
                }
                $arg = $type . '$' . $parameter->getName();
                if (array_key_exists($parameter->getPosition(), $defaults)) {
                    $arg .= ' = ' . var_export2($defaults[$parameter->getPosition()], true);
                }
                elseif (array_key_exists($parameter->getName(), $defaults)) {
                    $arg .= ' = ' . var_export2($defaults[$parameter->getName()], true);
                }
                elseif ($parameter->isDefaultValueAvailable()) {
                    $arg .= ' = ' . var_export2($parameter->getDefaultValue(), true);
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
            foreach (self::$constraintNamespaces as $namespace => $directories) {
                foreach ((array) $directories as $directory) {
                    foreach (file_list($directory, ['extension' => 'php']) ?: [] as $file) {
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
        }

        if ($types['variation']) {
            foreach (self::$constraintVariations as $name => $variation) {
                if ($variation === false) {
                    continue;
                }
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
            foreach (get_defined_functions(true) as $functions) {
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
                        $variation[$funcname] = '\\' . __CLASS__ . ' ' . $reffunc->getShortName() . ($n ? $n : '') . "(" . implode(', ', $params) . ")";
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
            $member = [];
            foreach ($methods as $key => $method) {
                if (!array_key_exists($key, $result)) {
                    $member[$key] = ' * @method ' . $method;
                }
            }
            if ($member) {
                $result[] = ' * @see ' . $name;
                $result = array_merge($result, $member);
                $result[] = ' *';
            }
        }
        return "/**\n" . implode("\n", $result) . "\n */";
    }

    public static function generateStub(string $inputdir, string $outputdir, int $dependLevel = 0)
    {
        $v = fn($v) => $v;
        $classname = Actual::class;

        $classes = [];
        foreach (is_file($inputdir) ? [$inputdir] : file_list($inputdir, ['extension' => 'php']) as $file) {
            $current = get_declared_classes();
            ob_start();
            require_once $file;
            ob_end_clean();
            $classes += array_flip(array_diff(get_declared_classes(), $current));
        }

        // gather recursive
        $foundClasses = $classes;
        for ($i = 0; $i < $dependLevel && count($foundClasses) > 0; $i++) {
            $dependsClasses = [];
            foreach ($foundClasses as $class => $dummy) {
                $refclass = new \ReflectionClass($class);

                if ($refclass->getParentClass()) {
                    $dependsClasses[$refclass->getParentClass()->getName()] = true;
                }
                $dependsClasses += array_flip($refclass->getTraitNames());
                $dependsClasses += array_flip($refclass->getInterfaceNames());

                foreach ($refclass->getProperties() as $property) {
                    $dependsClasses += array_flip(array_filter(array_map(fn($v) => $v->getName(), reflect_types($property->getType())->getTypes()), 'class_exists'));
                }

                foreach ($refclass->getMethods() as $method) {
                    $dependsClasses += array_flip(array_filter(array_map(fn($v) => $v->getName(), reflect_types($method->getReturnType())->getTypes()), 'class_exists'));
                }
            }
            $foundClasses = $dependsClasses;
            $classes += $dependsClasses;
        }

        $generated = [];
        $classes[$classname] = true;
        foreach ($classes as $class => $dummy) {
            $refclass = new \ReflectionClass($class);
            if ($refclass->isAnonymous()) {
                continue;
            }

            $stubname = "$outputdir/" . strtr($refclass->name, ['\\' => DIRECTORY_SEPARATOR]) . '.stub.php';
            $stubspace = "stub{$v($refclass->getNamespaceName() ? "\\" . $refclass->getNamespaceName() : '')}";
            $stubclass = "{$v($refclass->getShortName())}Stub";
            $stubparent = "{$v($refclass->getParentClass() ? "\\stub\\{$v($refclass->getParentClass()->getName())}Stub" : '')}";
            $generated[] = "\\$stubspace\\$stubclass";

            if (
                ($refclass->isInternal() && file_exists($stubname)) ||
                (!$refclass->isInternal() && file_exists($stubname) && filemtime($stubname) > filemtime($refclass->getFileName()))
            ) {
                continue;
            }

            $mixTypes = function (?\ReflectionType $type, \ReflectionClass $self) use ($classname) {
                $types = [];
                foreach (reflect_types($type)->getTypes() as $type) {
                    $name = $type->getName();
                    if ($type->isBuiltin()) {
                        $types[] = $name;
                    }
                    elseif (in_array($name, ['self', 'static'], true)) {
                        $types[] = "\\$self->name";
                        $types[] = $name;
                    }
                    else {
                        $types[] = "\\$name";
                        $types[] = "\\stub\\{$name}Stub";
                    }
                }
                $types[] = "\\$classname";
                return implode('|', $types);
            };

            $properties = [];
            foreach ($refclass->getProperties() as $property) {
                if ($property->getDeclaringClass()->getName() === $refclass->getName()) {
                    $properties[] = "/** @see \\$refclass->name::\$$property->name */";
                    $properties[] = "public {$v($property->isStatic() ? 'static ' : '')}{$v($mixTypes($property->getType(), $refclass))} \$$property->name;";
                }
            }

            $methods = [];
            foreach ($refclass->getMethods() as $method) {
                if (substr($method->name, 0, 2) === '__') {
                    continue;
                }
                if ($method->getDeclaringClass()->getName() === $refclass->getName()) {
                    $arguments = function_parameter($method);
                    try {
                        if ($arguments === function_parameter($method->getPrototype())) {
                            continue;
                        }
                    }
                    catch (\ReflectionException $e) {
                        // through (because ReflectionMethod::hasPrototype is from php8.2)
                    }

                    $methods[] = "/** @see \\$refclass->name::$method->name() */";
                    $methods[] = "public {$v($method->isStatic() ? 'static ' : '')}function $method->name({$v(implode(', ', $arguments))}): {$v($mixTypes($method->getReturnType(), $refclass))} { }";
                }
            }

            file_set_contents($stubname, <<<PHP
                <?php /** @noinspection PhpLanguageLevelInspection */
                
                namespace $stubspace;
                
                /**
                 * @mixin \\stub\\ryunosuke\\PHPUnit\\ActualStub
                 */
                class $stubclass{$v($stubparent ? " extends $stubparent" : '')}
                {
                    use \\ryunosuke\\PHPUnit\\Annotation;
                
                    {$v(implode("\n    ", $properties))}

                    {$v(implode("\n    ", $methods))}
                }
                
                PHP,);
        }

        file_set_contents("$outputdir/All.stub.php", <<<PHP
            <?php /** @noinspection PhpLanguageLevelInspection */

            namespace stub;
            
            /**
            {$v(implode("\n", array_map(fn($g) => " * @mixin $g", $generated)))}
             */
            class All
            {
            }
            
            PHP,);
    }

    private function create($actual, $arguments = []): Actual
    {
        $this->___doneSomething = true;

        $that = new static($actual);
        $that->___parent = $this;
        $that->___arguments = $arguments;
        $that->___results = $this->___results;

        return $that;
    }

    public static function __callStatic($name, $arguments): Actual
    {
        return (new static(last_value(static::$___objects)))->__call($name, $arguments);
    }

    public function __construct($actual)
    {
        $this->___actual = $actual;
        $this->___parent = $this;

        if (is_object($actual)) {
            static::$___objects[spl_object_id($this)] = get_class($actual);
        }
        elseif (is_string($actual) && !is_callable(trim($actual, '\\')) && class_exists(trim($actual, '\\'))) {
            static::$___objects[spl_object_id($this)] = (string) $actual;
        }
    }

    public function __destruct()
    {
        unset(static::$___objects[spl_object_id($this)]);

        if (!($this->___results['assertionCount'] ?? 0) && ($this->___actual instanceof UndefinedException)) {
            throw new RiskyTestError('This actual did not perform any assertions', 0, $this->___actual);
        }
        if (!$this->___doneSomething && $this->___actual instanceof \Throwable) {
            throw $this->___actual;
        }
        if (!$this->___outputAsserted && strlen($this->___output ?? '')) {
            echo $this->___output;
        }
        if (!$this->___errorAsserted && $this->___error instanceof Error) {
            throw $this->___error;
        }

        gc_collect_cycles();
    }

    public function __toString()
    {
        if (is_object($this->___actual)) {
            if (is_stringable($this->___actual) && strpos($this->getCallerLine(), '::') === false) {
                return (string) $this->___actual;
            }
            // delete future scope
            trigger_error('use $class::staticMethod()', E_USER_DEPRECATED);
            $staticCaller = new class(new static($this->___actual)) {
                public static $that;

                public function __construct($that)
                {
                    self::$that = $that;
                }

                public static function __callStatic($name, $arguments)
                {
                    return self::$that::$name(...$arguments);
                }
            };
            return get_class($staticCaller);
        }
        return var_export2($this->___actual, true) . "\n";
    }

    public function __call($name, $arguments)
    {
        if (self::$debugMode && $name === 'debug') {
            return $this->assert([$this->___actual], new StringContains(...$arguments));
        }

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

        $actuals = $modes['each'] ? $this->___actual : [$this->___actual];

        try {
            $callee = lcfirst($callee);
            if (isset(self::$constraintVariations[$callee])) {
                $variation = self::$constraintVariations[$callee];
                if ($variation === false) {
                    goto SKIP;
                }
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
        }
        catch (\TypeError|\ArgumentCountError $e) {
            // do nothing, fallback user method (e.g. count, isReadable, etc)
        }

        SKIP:

        if ($this->functionArgument($name) !== null) {
            return $this->function($name, ...$arguments);
        }

        if (preg_match('#' . preg_quote($name, '#') . '\s*\(\s*\.\.\.\s*\[#u', $this->getCallerLine())) {
            return $this->callable($name, ...$arguments);
        }

        return $this->try($name, ...$arguments);
    }

    public function __invoke(...$arguments): Actual
    {
        if (preg_match('#\)\(\s*\.\.\.\s*\[#u', $this->getCallerLine())) {
            return $this->callable(null, ...$arguments);
        }

        return $this->try(null, ...$arguments);
    }

    public function __get($name): Actual
    {
        if ($name === 'and') {
            return $this->and();
        }

        try {
            if (is_array($this->___actual)) {
                if (!array_key_exists($name, $this->___actual)) {
                    throw new UndefinedException("undefined array key ('$name')");
                }
                $var = $this->___actual[$name];
            }
            else {
                $var = $this->var($name);
            }
        }
        catch (\Throwable $ex) {
            $var = $ex;
        }

        return $this->create($var);
    }

    public function __set($name, $value)
    {
        if (is_array($this->___actual)) {
            $this->___actual[$name] = $value;
            return;
        }

        $refproperties = Util::reflectProperty($this->___actual, $name);
        foreach ($refproperties as $refproperty) {
            if ($refproperty->isStatic() || is_object($this->___actual)) {
                $refproperty->isStatic() ? $refproperty->setValue($value) : $refproperty->setValue($this->___actual, $value);
            }
        }
        if (!$refproperties) {
            $this->___actual->$name = $value;
        }
    }

    public function __unset($name)
    {
        if (is_array($this->___actual)) {
            unset($this->___actual[$name]);
            return;
        }
        unset($this->___actual->$name);
    }

    public function offsetGet($offset): Actual
    {
        if (is_array($this->___actual) || $this->___actual instanceof ArrayAccess) {
            return $this->create($this->___actual[$offset]);
        }

        throw new \DomainException('$this->actual must be structure value given ' . gettype($this->___actual) . ').');
    }

    public function offsetSet($offset, $value): void
    {
        $this->___actual[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->___actual[$offset]);
    }

    public function var(string $propertyname)
    {
        return Util::propertyToValue($this->___actual, $propertyname);
    }

    public function use(?string $methodname): callable
    {
        if ($methodname === null && !is_object($this->___actual) && is_callable($this->___actual)) {
            return \Closure::fromCallable($this->___actual);
        }
        return Util::methodToCallable($this->___actual, $methodname);
    }

    public function callable(?string $methodname = null, ...$bindings): Actual
    {
        $callable = $this->use($methodname);
        if ($bindings) {
            $callable = Util::selfDescribingCallable(function (&...$arguments) use ($callable, $bindings) {
                $arguments += $bindings;
                return $callable(...$this->unwrap($arguments));
            }, Util::callableToString($callable));
        }
        return $this->create($callable);
    }

    public function fn(...$bindings): Actual
    {
        return $this->callable(null, ...$bindings);
    }

    public function do(?string $methodname, ...$arguments): Actual
    {
        return $this->create($this->use($methodname)(...$this->unwrap($arguments)));
    }

    public function new(...$arguments): Actual
    {
        if (preg_match('#new\s*\(\s*\.\.\.\s*\[#u', $this->getCallerLine())) {
            return $this->callable('__construct', ...$arguments);
        }

        return $this->try('__construct', ...$arguments);
    }

    public function try(?string $methodname, ...$arguments): Actual
    {
        try {
            ob_start();
            $handler = set_error_handler(function ($code, $message, $file, $line) use (&$handler, &$error) {
                try {
                    return $handler($code, $message, $file, $line);
                }
                catch (\Throwable $t) {
                    $error = $t;
                }
            });
            $time = microtime(true);
            $return = $this->use($methodname)(...$this->unwrap($arguments));
        }
        catch (\Throwable $t) {
            $return = $t;
        }
        finally {
            $time = microtime(true) - $time;
            restore_error_handler();
            $output = ob_get_clean();
        }
        $that = $this->create($return, $arguments);
        $that->___elapsed = $time;
        $that->___output = $output;
        $that->___error = $error;
        return $that;
    }

    public function insteadof(callable $callback)
    {
        return $this->create($callback($this->___actual));
    }

    public function list($index = null): Actual
    {
        if ($index === null) {
            return $this->create($this->___arguments);
        }
        return $this->create($this->___arguments[$index]);
    }

    /**
     * @deprecated
     *
     * @param callable $function
     * @param mixed ...$arguments
     * @return Actual
     */
    public function function ($function, ...$arguments): Actual
    {
        return $this->create(chain($this->___actual)->$function(...$arguments)(), $arguments);
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
        foreach ($this->___actual as $k => $actual) {
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
        return $this->___actual;
    }

    public function echo(): Actual
    {
        var_pretty($this->___actual);
        return $this;
    }

    public function eval(Constraint ...$constraints): Actual
    {
        return $this->assert([$this->___actual], ...$constraints);
    }

    public function as(...$messages): Actual
    {
        $message = '';
        foreach ($messages as $m) {
            $message .= is_stringable($m) ? $m : var_export2($m, true);
        }
        $this->___message = $message;
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
            $that = $that->___parent;
        } while (--$nest > 0);
        return $that;
    }

    public function final(string $mode = ''): Actual
    {
        return $this->create($this->___results[$mode] ?? $this->___results);
    }

    public function wasOutputed(string $expectedOutput = ''): Actual
    {
        $this->___outputAsserted = true;
        return $this->assert([$this->___output ?? ''], new StringContains($expectedOutput));
    }

    public function wasErrored(string $expectedMessage = ''): Actual
    {
        $this->___errorAsserted = true;
        return $this->assert([$this->___error ? $this->___error->getMessage() : ''], new StringContains($expectedMessage));
    }

    public function inElapsedTime(float $elapsedTime = 0): Actual
    {
        return $this->assert([$this->___elapsed ?? 0], LogicalOr::fromConstraints(new IsEqual($elapsedTime), new LessThan($elapsedTime)));
    }

    public function declare($hint = ''): Actual
    {
        $receiver = '';
        $rewriter = function ($line) use ($hint, &$receiver) {
            $v = fn($v) => $v;
            $ve = fn($v) => var_export2($v, true);

            $actual = $this->___actual;
            if (is_object($actual) && (new \ReflectionClass($actual))->isAnonymous()) {
                throw new UndefinedException("undetect({$v(get_class($actual))})");
            }
            elseif (is_null($actual)) {
                $method = "isNull";
                $params = [];
            }
            elseif (is_bool($actual)) {
                $method = "is{$v(ucfirst($ve($actual)))}";
                $params = [];
            }
            elseif (is_float($actual)) {
                $method = "isBetween";
                $params = [$ve(floor($actual)), $ve(ceil($actual))];
            }
            elseif ($actual instanceof UndefinedException) {
                $method = "isUndefined";
                $params = [];
            }
            elseif ($actual instanceof \Throwable) {
                $method = "isThrowable";
                $class = "new \\{$v(get_class($actual))}";
                $params = ["{$class}({$ve($actual->getMessage())}" . ($actual->getCode() ? ", {$ve($actual->getCode())})" : ")")];
            }
            elseif (is_stringable($actual) && str_exists($hint, 'length')) {
                $method = "stringLengthEquals";
                $params = [strlen($actual)];
            }
            elseif (is_stringable($actual) && str_exists($hint, 'html')) {
                $method = "htmlMatchesArray";
                $params = [$ve(HtmlMatchesArray::stringToArray($actual))];
            }
            elseif (is_stringable($actual) && str_exists($hint, 'json')) {
                $method = "jsonMatchesArray";
                $params = [$ve(json_decode($actual, true))];
            }
            elseif (is_countable($actual) && str_exists($hint, 'count')) {
                $method = "count";
                $params = [count($actual)];
            }
            elseif (is_object($actual) && !$actual instanceof \stdClass) {
                $method = "isInstanceOf";
                $params = ["\\{$v(get_class($actual))}::class"];
            }
            else {
                $method = "is";
                $params = [$ve($actual)];
            }

            $receiver = "{$method}({$v(implode(', ', $params))})";
            preg_match('#^(\s*)#', $line, $matches);
            $newline = preg_replace('#(|::)\s*declare\s*\(.*?\)#u', '$1' . $receiver, $line);
            $newline = preg_replace('#\n#u', "\n{$v($matches[1] ?? '')}", $newline);
            return self::$debugMode ? $line : $newline;
        };

        $this->getCallerLine($rewriter);
        return self::$debugMode ? $this->create($receiver) : $this;
    }

    public function break(bool $breakable = true): Actual
    {
        $this->___breakable = $breakable;
        return $this;
    }

    private function assert($actuals, Constraint ...$constraints): Actual
    {
        $constraint = LogicalOr::fromConstraints(...$constraints);

        // memory_reset_peak_usage(); // Not implemented yet
        $memory = memory_get_usage();
        $assertionCount = Assert::getCount();
        $time = microtime(true);
        $before = getrusage();

        $this->___doneSomething = true;
        foreach ($actuals as $actual) {
            try {
                Assert::assertThat($actual, $constraint, $this->___message);
            }
            catch (AssertionFailedError $e) {
                if (!$this->___breakable) {
                    throw $e;
                }

                $prev = null;
                foreach (debug_backtrace() as $step) {
                    if (($step['object'] ?? null) instanceof TestCase) {
                        assert($step['object'] instanceof TestCase);
                        $step['object']->addWarning($e->getMessage() . " in {$prev['file']}:{$prev['line']}");
                        break;
                    }
                    $prev = $step;
                }
            }
        }

        $after = getrusage();
        $elapsed = microtime(true) - $time;
        $cpu_usr = $after['ru_utime.tv_sec'] - $before['ru_utime.tv_sec'] + ($after['ru_utime.tv_usec'] - $before['ru_utime.tv_usec']) / 1000 / 1000;
        $cpu_sys = $after['ru_stime.tv_sec'] - $before['ru_stime.tv_sec'] + ($after['ru_stime.tv_usec'] - $before['ru_stime.tv_usec']) / 1000 / 1000;
        $this->___results = array_replace($this->___results, [
            'time'           => $elapsed,
            'cpu'            => ($cpu_usr + $cpu_sys) / $elapsed,
            'cpuUser'        => $cpu_usr / $elapsed,
            'cpuSystem'      => $cpu_sys / $elapsed,
            'memory'         => memory_get_peak_usage() - $memory,
            'assertionCount' => Assert::getCount() - $assertionCount,
        ]);

        return $this;
    }

    private function &unwrap(array &$arguments): array
    {
        foreach ($arguments as &$argument) {
            $argument = $argument instanceof Actual ? $argument->return() : $argument;
        }
        return $arguments;
    }

    /**
     * @deprecated
     */
    private function functionArgument($function): ?string
    {
        if (strpos($function, '->') === 0 || strpos($function, '::') === 0) {
            return substr($function, 2);
        }

        foreach (self::$functionNamespaces as $namespace => $patterns) {
            $patterns = (array) $patterns;
            if ($patterns) {
                $allows = array_filter($patterns, fn($p) => ($p[0] ?? '') !== '!');
                $denies = array_map(fn($p) => substr($p, 1), array_filter($patterns, fn($p) => ($p[0] ?? '') === '!'));
                if (!fnmatch_or($allows ?: '*', $function, FNM_NOESCAPE) || fnmatch_or($denies ?: '', $function, FNM_NOESCAPE)) {
                    return null;
                }
            }

            $fname = trim(trim($namespace, '\\') . "\\$function", '\\');
            if (is_callable($fname)) {
                return $fname;
            }

            if (preg_match('#(.+?)(\d)$#', $fname, $match) && is_callable($match[1])) {
                return $fname;
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

    private function getCallerLine($rewriter = null, ?array $trace = null): string
    {
        if ($trace === null) {
            $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            foreach ($traces as $trace) {
                if ($trace['file'] !== __FILE__) {
                    break;
                }
            }
        }
        $file = $trace['file'];
        $line = $trace['line'];

        static $files = [];
        $files[$file] ??= [
            'fixed' => true,
            'lines' => file($file, FILE_IGNORE_NEW_LINES),
        ];
        foreach (array_slice(array_filter($files, fn($member) => $member['fixed']), 0, -4) as $n => $member) {
            unset($files[$n]);
        }

        if ($rewriter) {
            $newline = $rewriter($files[$file]['lines'][$line - 1]);
            if ($newline !== $files[$file]['lines'][$line - 1]) {
                $files[$file]['fixed'] = false;
                $files[$file]['lines'][$line - 1] = $newline;
                file_put_contents($file, implode("\n", $files[$file]['lines']) . "\n");
            }
            return $newline;
        }

        return $files[$file]['lines'][$line - 1];
    }

    public function __isset($name) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetExists($offset): bool { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }
}
