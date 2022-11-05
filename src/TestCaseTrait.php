<?php

namespace ryunosuke\PHPUnit;

use Closure;
use PHPUnit\Framework\SkippedTestError;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RuntimeException;

trait TestCaseTrait
{
    /**
     * reset function base's value on temporary scope
     *
     * `unset($return)` restores original value
     *
     * @see https://phpunit.readthedocs.io/ja/latest/annotations.html#appendixes-annotations-backupglobals
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

        $ref = new ReflectionClass($this);
        $refprop = $ref->getProperty('backupGlobals');
        $refprop->setAccessible(true);
        if ($refprop->getValue($this)) {
            $GLOBALS[__FUNCTION__][] = $handler;
        }
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
     * return environment variable or skip
     *
     * @param string $envName
     * @return string
     */
    public function getEnvOrSkip(string $envName): ?string
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
    public function getConstOrSkip(string $constantName)
    {
        if (!defined($constantName)) {
            throw new SkippedTestError("constant '$constantName' is not defined");
        }
        return constant($constantName);
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
}
