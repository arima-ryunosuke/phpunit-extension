<?php

namespace ryunosuke\PHPUnit;

use PHPUnit\Framework\Assert;

/**
 * Run test by DocComment
 *
 * enable use 2 annotations and run codeblock.
 *
 * - @that: simply call the method/function
 * - @test: exec code block that dependency context
 * - ```php ~ ```: exec code block that dependency context
 *
 * - e.g. @that(1, 2, 3)->is(6)
 * - e.g. @test {
 *     $this->method()->isTrue();
 *     $that(1, 2, 3)->is(6);
 * }
 * - e.g. @test context {
 *     $this->method()->isTrue();
 *     $that(1, 2, 3)->is(6);
 * }
 *
 * ```php
 * // run this code block
 * $this->method()->isTrue();
 * $that(1, 2, 3)->is(6);
 * ```
 *
 * '$this' is binded by the method's instance and actual.
 * the instance is resolved by dependency container.
 *
 * '$that' means the method/function.
 * in case method, the instance is resolved by dependency container.
 *
 * any other '$varname' are resolved by dependency container.
 *
 * see \ryunosuke\Test\AnnotesterTest testcase for details.
 */
class Annotester
{
    private $resolver = [];
    private $locals   = [];
    private $options  = [];

    /** @var \ReflectionFunctionAbstract current testing method/function */
    private $target;
    private $context;

    /**
     * constructor
     *
     * $resolver provides Instance or local variables.
     *
     * - e.g. ['ClassName' => [$arg1, $arg2, ... , $argX]]
     *     - mean new ClassName($arg1, $arg2, ... , $argX)
     * - e.g. ['ClassName' => ['context1' => [$arg1, $arg2, ... , $argX], 'context2' => [$arg1, $arg2, ... , $argX]]]
     *     - mean new ClassName($arg1, $arg2, ... , $argX) by context
     *     - context is specified by @test context {...}
     * - e.g. ['ClassName' => function ($arg1, $arg2, ... , $argX) {}]
     *     - mean result of Closure
     *     - closure's argument is resolved recursive by typehint or varname
     * - e.g. ['ClassName' => $instance]
     *     - mean $instance directly
     * - e.g. ['$var' => 123]
     *     - mean local $var
     *
     * $options is specified output option mainly.
     *
     * - outdir: specified output directory.
     * - format: specified filename format.
     * - doccode: specified testcode regex.
     *
     * @param array $resolver dependency container
     * @param array $options other options
     */
    public function __construct(array $resolver = [], array $options = [])
    {
        // '' forces function invoker
        $this->resolver = $resolver;
        $this->resolver[''] = new class() {
            public function __call($name, $arguments) { return $name(...$arguments); }
        };

        // gather local var
        foreach ($this->resolver as $k => $v) {
            if (($k[0] ?? null) === '$') {
                $this->locals[substr($k, 1)] = $v;
            }
        }

        // set options
        $this->options = array_replace([
            'outdir'  => sys_get_temp_dir() . '/annotester',
            'format'  => null,
            'doccode' => "#^```php(:(?<context>.*?)\\R)?(?<phpcode>.*?)^```#ums",
        ], $options);

        // default settings
        if ($this->options['outdir'] !== null && !file_exists($this->options['outdir'])) {
            mkdir($this->options['outdir'], 0777, true);
        }
        if ($this->options['format'] === null) {
            $this->options['format'] = function (\Reflector $target) {
                $id = concat($target->class ?? '', '::') . $target->name . '.php';
                return strtr($id, ['\\' => '/', '::' => '-']);
            };
        }
    }

    public function __invoke($context, \Closure $callback)
    {
        $this->context = $context;

        if (is_string($this->target)) {
            $actual = $this;
        }
        elseif ($this->target instanceof \ReflectionClass) {
            $actual = new Actual($this->resolve($this->target->name));
        }
        elseif ($this->target instanceof \ReflectionFunction) {
            $actual = new Actual($this->resolve(''));
        }
        elseif ($this->target->isStatic()) {
            $actual = new Actual($this->target->class);
        }
        else {
            $actual = new Actual($this->resolve($this->target->class));
        }

        return $callback->call($actual, function (...$arguments) use ($actual) {
            return $actual->callable($this->target->name)(...$arguments);
        }, $this->locals);
    }

    /**
     * test specified Reflection DocComment
     *
     * specify ReflectionFunction or ReflectionMethod or ReflectionClass or phpcode.
     * in case specified string, autodetect Reflection*** or directly code.
     *
     * if ReflectionClass, test class's methods that filtered by $filter argument.
     * $filter means below.
     *
     * - null:    nofilter. test all methods.
     * - int:     filter method modifier. e.g. ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC
     * - string:  filter method name by fnmatch. e.g. "get*"
     * - Closure: filter method by Closure result.
     *
     * @param \Reflector|string $target test target
     * @param null|int|string|\Closure $filter filter condition in case ReflectionClass
     * @return int assertion count
     */
    public function test($target, $filter = null): int
    {
        // string to reflection suitably
        if (is_string($target)) {
            [$class, $method] = explode('::', $target) + [1 => null];
            if (function_exists($class)) {
                $target = new \ReflectionFunction($class);
            }
            elseif (method_exists($class, $method)) {
                $target = new \ReflectionMethod($class, $method);
            }
            elseif (class_exists($class)) {
                $target = new \ReflectionClass($class);
            }
        }

        // parse doccomment
        if ($target instanceof \Reflector) {
            $see = '\\' . concat($target->class ?? '', "::") . $target->name . '()';
            $filename = $this->options['format']($target);

            $tags = parse_annotation($target, [
                'that' => function ($value) { return "    \$that$value"; },
                'test' => true,
            ]);
            $tests = array_merge((array) ($tags['that'] ?? []), (array) ($tags['test'] ?? []));

            if ($this->options['doccode'] !== null) {
                $doccomment = preg_replace('#^\\s+\\*\s#ums', '', $target->getDocComment());
                preg_match_all($this->options['doccode'], $doccomment, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $code = indent_php("\n" . trim($match['phpcode']), 4);
                    if (isset($match['context']) && strlen($match['context'])) {
                        $tests[$match['context']] = $code;
                    }
                    else {
                        $tests[] = $code;
                    }
                }
            }
        }
        // directly
        else {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
            $see = $trace['file'] . '#' . $trace['line'];
            $filename = sha1($target) . '.php';

            $tests = [indent_php("\n" . trim($target), 4)];
        }

        // gather code
        $codes = [];
        foreach ($tests as $name => $test) {
            $locals = [];
            $stmt = "";
            $tokens = parse_php(implode("\n", (array) $test));
            array_shift($tokens);
            foreach ($tokens as $n => $tokenarray) {
                [$id, $token] = $tokenarray;
                if ($id === T_VARIABLE && array_key_exists($token, $this->resolver)) {
                    $typename = var_type($this->resolver[$token], true);
                    $locals[$token] = "/** @var $typename $token */";
                }
                $stmt .= $token;
            }
            $context = is_int($name) ? '""' : var_export($name, true);
            $vars = concat("    ", implode("\n    ", $locals), "\n    extract(\$locals);\n\n");
            $stmt = trim($stmt, ";\n") . ';';
            $codes[] = "\$this($context, function (\$that, \$locals) {\n{$vars}$stmt\n});";
        }
        $code = sprintf("
/**
 * This file is auto generated by Annotester
 *
 * @see $see
 */

%s
",
            implode("\n\n", $codes)
        );

        // write and run
        $this->target = $target;
        $outpath = $this->options['outdir'] . "/$filename";
        file_set_contents($outpath, "<?php\n$code");
        $result = Assert::getCount();
        require $outpath;
        $result = Assert::getCount() - $result;

        // recursive filtered methods
        if ($target instanceof \ReflectionClass) {
            assert(is_null($filter) || is_int($filter) || is_string($filter) || $filter instanceof \Closure);
            foreach ($target->getMethods() as $method) {
                if (is_int($filter) && ($method->getModifiers() & $filter) !== $filter) {
                    continue;
                }
                elseif (is_string($filter) && !fnmatch($filter, $method->name)) {
                    continue;
                }
                elseif ($filter instanceof \Closure && !$filter($method)) {
                    continue;
                }
                $result += $this->test($method);
            }
        }

        return $result;
    }

    private function resolve(string $name)
    {
        if (class_exists($name)) {
            if (!array_key_exists($name, $this->resolver)) {
                return new $name();
            }
            $value = $this->resolver[$name];
            if (!is_indexarray($value)) {
                $value = $value[$this->context];
            }
            if ($value instanceof \Closure) {
                $refunc = new \ReflectionFunction($value);
                $params = [];
                foreach ($refunc->getParameters() as $parameter) {
                    /** @var \ReflectionNamedType $type */
                    $type = $parameter->getType();
                    if ($type && array_key_exists($pname = $type->getName(), $this->resolver)) {
                        $params[] = $this->resolve($pname);
                    }
                    elseif (array_key_exists($pname = '$' . $parameter->getName(), $this->resolver)) {
                        $params[] = $this->resolve($pname);
                    }
                    elseif ($parameter->isDefaultValueAvailable()) {
                        $params[] = $parameter->getDefaultValue();
                    }
                }
                return $value(...$params);
            }
            if (is_array($value)) {
                return new $name(...$value);
            }
        }

        assert(array_key_exists($name, $this->resolver));
        return $this->resolver[$name];
    }
}
