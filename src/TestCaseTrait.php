<?php

namespace ryunosuke\PHPUnit;

use Closure;
use ErrorException;
use PHPUnit\Framework\SkippedTestError;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionFunction;
use RuntimeException;
use ryunosuke\PHPUnit\Printer\AbstractPrinter;
use SebastianBergmann\Comparator\ObjectComparator;
use stdClass;
use Throwable;

trait TestCaseTrait
{
    private Closure $throwableHandler;

    /**
     * set Throwable trap handler
     *
     * trap allows handling of the specified exception
     * when class-string specified, catch it and throw $thrownClass
     * when other string specified, catch it's message and throw $thrownClass
     * if $thrownObject is null then do nothing
     *
     * @param callable|string $handler
     * @param Throwable|string $thrownObject
     */
    public function trapThrowable($handler, $thrownObject = null): void
    {
        if (is_string($handler)) {
            $handler = function (Throwable $t) use ($handler, $thrownObject) {
                if (!(class_exists($handler) && $t instanceof $handler) && (strpos($t->getMessage(), $handler) === false)) {
                    throw $t; // @codeCoverageIgnore
                }
                if ($thrownObject !== null) {
                    throw is_object($thrownObject) ? $thrownObject : new $thrownObject("trapped recognized $handler"); // @codeCoverageIgnore
                }
            };
        }

        $this->throwableHandler = Closure::fromCallable($handler);
    }

    protected function onNotSuccessfulTest(Throwable $t): void
    {
        if (!isset($this->throwableHandler)) {
            throw $t; // @codeCoverageIgnore
        }

        $refhandler = new ReflectionFunction($this->throwableHandler);
        $param = $refhandler->getParameters()[0] ?? null;
        if ($param && $param->hasType()) {
            $type = reflect_types($param);
            if (!$type->allows($t)) {
                throw $t; // @codeCoverageIgnore
            }
        }
        ($this->throwableHandler)($t);
    }

    /**
     * call closure at Test end
     *
     * This is almost the same as tearDown.
     * However it can be set individually for each test case.
     *
     * @param callable $finalizer
     */
    public function finalize(callable $finalizer)
    {
        // TestCase->customComparators is cleared per testcases. it means __destruct is called
        // This is hack(bad move), but can be easily implemented without modifying PHPUnit
        $this->registerComparator(new class($finalizer) extends ObjectComparator {
            private $finalizer;

            public function __construct(callable $finalizer)
            {
                parent::__construct();
                $this->finalizer = $finalizer;
            }

            public function __destruct()
            {
                if (isset($this->finalizer)) {
                    ($this->finalizer)();
                    unset($this->finalizer);
                }
                gc_collect_cycles();
            }

            public function accepts($expected, $actual) { return false; } // @codeCoverageIgnore because never call
        });
    }

    /**
     * reset function base's value on temporary scope
     *
     * `unset($return)` restores original value
     *
     * @param callable $setter
     * @param array $initializeArgs
     * @param ?array $finalizeArgs
     * @return callable
     */
    public function restorer(callable $setter, array $initializeArgs, ?array $finalizeArgs = null): callable
    {
        $current = $setter(...$initializeArgs);
        $handler = new class($setter, $finalizeArgs ?? [$current]) {
            private $setter;
            private $args;
            private $restored = false;

            public function __construct($setter, $args)
            {
                $this->setter = $setter;
                $this->args = $args;
            }

            public function __destruct()
            {
                $this();

                unset($this->finalize);
                unset($this->args);
                gc_collect_cycles();
            }

            public function __invoke()
            {
                if (!$this->restored) {
                    ($this->setter)(...$this->args);
                }
                $this->restored = true;
            }
        };

        $this->finalize(fn() => $handler());
        return $handler;
    }

    /**
     * rewrite private property on temporary scope
     *
     * `unset($return)` restores original value
     *
     * @see https://phpunit.readthedocs.io/ja/latest/annotations.html#appendixes-annotations-backupstaticattributes
     *
     * @param string|object $eitherClassOrObject
     * @param string $property
     * @param ?Closure $rewriter
     * @return object
     */
    public function rewriteProperty($eitherClassOrObject, string $property, ?Closure $rewriter = null): object
    {
        $handler = new class($eitherClassOrObject, $property) {
            private $instance;
            private $refproperty;
            private $backup;

            public function __construct($eitherClassOrObject, $property)
            {
                $this->instance = $eitherClassOrObject;
                $refclass = new ReflectionClass($this->instance);
                for ($class = $refclass; $class; $class = $class->getParentClass()) {
                    if ($class->hasProperty($property)) {
                        $this->refproperty = $class->getProperty($property);
                        $this->refproperty->setAccessible(true);
                        $this->backup = $this->get();
                        break;
                    }
                }
            }

            public function __destruct()
            {
                $this->set($this->backup);

                unset($this->instance);
                unset($this->refproperty);
                unset($this->backup);
                gc_collect_cycles();
            }

            public function get()
            {
                if ($this->refproperty->isStatic()) {
                    return $this->refproperty->getValue();
                }
                else {
                    return $this->refproperty->getValue($this->instance);
                }
            }

            public function set($value)
            {
                if ($this->refproperty->isStatic()) {
                    return $this->refproperty->setValue($value);
                }
                else {
                    return $this->refproperty->setValue($this->instance, $value);
                }
            }
        };

        $rewriter ??= fn($v) => $v;
        $handler->set($rewriter($handler->get()));
        return $handler;
    }

    /**
     * try method and return throwable
     *
     * private/protected methods can also be called.
     * $defaults is Default value when omitted.
     *
     * @param callable $callable
     * @param array $defaults
     * @return Closure
     */
    public function tryableCallable($callable, ...$defaults): Closure
    {
        $reffunc = reflect_callable($callable);
        return function (...$args) use ($callable, $reffunc, $defaults) {
            $names = [];
            foreach ($reffunc->getParameters() as $parameter) {
                $names[$parameter->getName()] = $parameter;

                if (array_key_exists($parameter->getPosition(), $args)) {
                    $args[$parameter->getName()] = $args[$parameter->getPosition()];
                    unset($args[$parameter->getPosition()]);
                }
                if (array_key_exists($parameter->getPosition(), $defaults)) {
                    $defaults[$parameter->getName()] = $defaults[$parameter->getPosition()];
                    unset($defaults[$parameter->getPosition()]);
                }
            }

            $arguments = array_shrink_key($names, $args + $defaults);
            if ($reffunc->isVariadic()) {
                $diff = array_diff_key($args + $defaults, $names);
                if ($diff && is_indexarray($diff)) {
                    $arguments = array_merge(array_values($arguments), $diff);
                }
                else {
                    $arguments = $arguments + $diff;
                }
            }

            try {
                set_error_handler(static function ($errno, $errstr, $errfile, $errline) {
                    if (!(error_reporting() & $errno)) {
                        return false;
                    }
                    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
                });
                return $reffunc(...$arguments);
            }
            catch (Throwable $t) {
                return $t;
            }
            finally {
                restore_error_handler();
            }
        };
    }

    /**
     * return environment variable or skip
     *
     * @param string $envName
     * @return string
     */
    public static function getEnvOrSkip(string $envName): ?string
    {
        $envvar = getenv($envName);
        if (strlen("$envvar") === 0) {
            throw new SkippedTestError("env '$envName' is not defined");
        }
        return $envvar;
    }

    /**
     * return constant value or skip
     *
     * @param string $constantName
     * @return mixed
     */
    public static function getConstOrSkip(string $constantName)
    {
        if (!defined($constantName)) {
            throw new SkippedTestError("constant '$constantName' is not defined");
        }
        return constant($constantName);
    }

    public static function getClassMap()
    {
        return class_map();
    }

    public static function getClassByDirectory(string $directory): array
    {
        $directory = realpath($directory) . DIRECTORY_SEPARATOR;

        return array_keys(array_filter(self::getClassMap(), fn($filename) => starts_with($filename, $directory)));
    }

    public static function getClassByNamespace(string $namespace): array
    {
        if (type_exists($namespace)) {
            $namespace = (new ReflectionClass($namespace))->getNamespaceName();
        }

        return array_keys(array_filter(self::getClassMap(), fn($classname) => starts_with($classname, $namespace), ARRAY_FILTER_USE_KEY));
    }

    /**
     * ready empty directory
     *
     * @param string|null $tmpdir
     * @return string
     */
    public function emptyDirectory(?string $tmpdir = null): string
    {
        clearstatcache(true);

        $tmpdir ??= sys_get_temp_dir();
        $dirname = strtr(get_class($this), ['\\' => '/']);
        $endname = urlencode($this->getName(false));
        $directory = "$tmpdir/$dirname/$endname";

        @mkdir($directory, 0777, true);
        if (!is_dir($directory)) {
            throw new RuntimeException("failed to mkdir '$directory'"); // @codeCoverageIgnore
        }

        $rdi = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($rii as $entry) {
            if ($entry->isLink() || $entry->isFile()) {
                unlink($entry);
            }
            else {
                rmdir($entry);
            }
        }

        return realpath($directory);
    }

    /**
     * do closure asynchronous
     *
     * @param Closure $closure
     * @return \ProcessAsync
     */
    public function backgroundTask(Closure $closure)
    {
        // unset $this bind. because TestCase is huge object
        if (is_bindable_closure($closure)) {
            $closure = $closure->bindTo(new stdClass(), 'static');
        }

        $autoloder = var_export(auto_loader(), true);
        $task = var_export3($closure, [
            'format'  => 'pretty',
            'outmode' => null,
            'return'  => true,
        ]);
        $scriptfile = sys_get_temp_dir() . '/backgroundTask' . spl_object_id($closure) . '.php';
        file_put_contents($scriptfile, "<?php\nrequire_once $autoloder;\n$task();");
        $process = process_async(PHP_BINARY, [$scriptfile]);

        $this->finalize(fn() => $process->terminate());
        return $process;
    }

    /**
     * report as phpunit's final header (only ProgressPrinter)
     *
     * @param mixed $message report content
     */
    public function report($message)
    {
        // delegate to AbstractPrinter for portability
        AbstractPrinter::report($this, $message);
    }
}
