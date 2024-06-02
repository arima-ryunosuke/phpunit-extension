<?php

namespace ryunosuke\Test;

use DomainException;
use ErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\SkippedTestError;
use RuntimeException;
use ryunosuke\PHPUnit\TestCaseTrait;
use UnexpectedValueException;

class TestCaseTraitTest extends \ryunosuke\Test\AbstractTestCase
{
    use TestCaseTrait;

    function test_trapThrowable_string_and_do_nothing()
    {
        $this->trapThrowable(RuntimeException::class);

        $this->assertEquals('dummy', 'dummy');
        throw new RuntimeException();
    }

    function test_trapThrowable_string_and_skip()
    {
        $this->trapThrowable(RuntimeException::class, SkippedTestError::class);

        $this->assertEquals('dummy', 'dummy');
        throw new RuntimeException();
    }

    function test_trapThrowable_string_nomatch()
    {
        $this->trapThrowable(RuntimeException::class);

        $this->expectException(UnexpectedValueException::class);
        throw new UnexpectedValueException();
    }

    function test_trapThrowable_notype()
    {
        $this->trapThrowable(function ($t) {
            $this->assertEquals(RuntimeException::class, get_class($t));
        });

        throw new RuntimeException();
    }

    function test_trapThrowable_type_match()
    {
        $this->trapThrowable(function (RuntimeException $t) {
            $this->assertEquals(UnexpectedValueException::class, get_class($t));
        });

        throw new UnexpectedValueException();
    }

    function test_trapThrowable_type_nomatch()
    {
        $this->trapThrowable(function (DomainException $t) {
            $this->fail();
        });

        $this->expectException(UnexpectedValueException::class);
        throw new UnexpectedValueException();
    }

    function test_trapThrowable_canceled()
    {
        $this->expectException(InvalidArgumentException::class);
        throw new InvalidArgumentException();
    }

    function test_finalize()
    {
        $GLOBALS['hoge'] = 'HOGE';
        $GLOBALS['fuga'] = 'FUGA';

        $this->finalize(function () {
            unset($GLOBALS['hoge']);
        });
        $this->finalize(function () {
            unset($GLOBALS['fuga']);
        });

        $this->assertTrue(isset($GLOBALS['hoge']));
        $this->assertTrue(isset($GLOBALS['fuga']));
    }

    /**
     * @depends test_finalize
     */
    function test_finalize_recovery()
    {
        $this->assertFalse(isset($GLOBALS['hoge']));
        $this->assertFalse(isset($GLOBALS['fuga']));
    }

    function test_restorer()
    {
        $currents = [ini_get('memory_limit'), mb_internal_encoding()];

        // change something
        $restorer1 = $this->restorer(fn($v) => ini_set('memory_limit', $v), ['99G']);
        $restorer2 = $this->restorer('mb_internal_encoding', ['SJIS'], [mb_internal_encoding()]);

        // enable changed value (see also test_restorer_php)
        $this->assertEquals('99G', ini_get('memory_limit'));
        $this->assertEquals('SJIS', mb_internal_encoding());

        // recovery explicit
        $restorer1();
        $this->assertEquals($currents[0], ini_get('memory_limit'));

        // recovery implicit (backupGlobals=on, so not recovery)
        unset($restorer2);
        $this->assertEquals('SJIS', mb_internal_encoding());

        return $currents[1];
    }

    /**
     * @depends test_restorer
     */
    function test_restorer_recovery($mb_internal_encoding)
    {
        // auto restore changes of test_restorer
        $this->assertEquals($mb_internal_encoding, mb_internal_encoding());
    }

    function test_rewriteProperty()
    {
        $object = new class() {
            private static $staticProperty  = 'this is static';
            private        $privateProperty = 'this is private';

            public function describe()
            {
                return [
                    'static'  => self::$staticProperty,
                    'private' => $this->privateProperty,
                ];
            }
        };

        $this->assertEquals([
            "static"  => "this is static",
            "private" => "this is private",
        ], $object->describe());

        $static = $this->rewriteProperty($object, 'staticProperty', fn($property) => $property . ' Ex');
        $this->assertEquals([
            "static"  => "this is static Ex",
            "private" => "this is private",
        ], $object->describe());

        $private = $this->rewriteProperty($object, 'privateProperty', fn($property) => $property . ' Ex');
        $this->assertEquals([
            "static"  => "this is static Ex",
            "private" => "this is private Ex",
        ], $object->describe());

        unset($private);
        $this->assertEquals([
            "static"  => "this is static Ex",
            "private" => "this is private",
        ], $object->describe());

        unset($static);
        $this->assertEquals([
            "static"  => "this is static",
            "private" => "this is private",
        ], $object->describe());
    }

    function test_tryableCallable()
    {
        $object = new class() {
            protected function simple($a, $b, $c)
            {
                return implode('', [$a, $b, $c]);
            }

            protected function default($a = 'a', $b = 'b', $c = 'c')
            {
                return implode('', [$a, $b, $c]);
            }

            protected function variadic($a, ...$bcd)
            {
                return implode('', array_merge([$a], $bcd));
            }

            protected function error()
            {
                trigger_error('ng');
                return 'ok';
            }

            protected function throw()
            {
                throw new RuntimeException('ex');
            }
        };

        $method = $this->tryableCallable([$object, 'simple'], c: 'c');
        $this->assertEquals('abc', $method('a', 'b'));
        $this->assertEquals('abC', $method('a', 'b', 'C'));
        $this->assertEquals('abc', $method(b: 'b', a: 'a'));

        $method = $this->tryableCallable([$object, 'default'], c: 'C');
        $this->assertEquals('abC', $method());
        $this->assertEquals('aBc', $method('a', c: 'c', b: 'B'));

        $method = $this->tryableCallable([$object, 'default'], 'A', 'B');
        $this->assertEquals('ABc', $method());
        $this->assertEquals('AbC', $method(c: 'C', b: 'b'));

        $method = $this->tryableCallable([$object, 'variadic']);
        $this->assertEquals('ac', $method('a', c: 'c'));
        $this->assertEquals('acb', $method('a', c: 'c', b: 'b'));

        $method = $this->tryableCallable([$object, 'variadic'], 'A');
        $this->assertEquals('Ac', $method(c: 'c'));
        $this->assertEquals('Acb', $method(c: 'c', b: 'b'));

        $method = $this->tryableCallable([$object, 'variadic'], b: 'b');
        $this->assertEquals('acb', $method('a', c: 'c'));
        $this->assertEquals('aBcb', $method('a', 'B', c: 'c'));
        $this->assertEquals('ab', $method('a'));

        $method = $this->tryableCallable([$object, 'variadic'], z: 'z');
        $this->assertEquals('abcz', $method('a', 'b', c: 'c'));
        $this->assertEquals('ayxz', $method('a', y: 'y', x: 'x'));

        $method = $this->tryableCallable([$object, 'error']);
        $this->assertEquals(new ErrorException('ng', 0, E_USER_NOTICE), $method());
        $this->assertEquals('ok', @$method());

        $method = $this->tryableCallable([$object, 'throw']);
        $this->assertEquals(new RuntimeException('ex'), $method());

        $closure = $this->tryableCallable(function ($a, $b, $c = 'c', ...$z) { return implode('', array_merge([$a, $b, $c], $z)); }, z: 'z');
        $this->assertEquals('abcz', $closure('a', 'b'));
        $this->assertEquals('abCz', $closure('a', 'b', 'C'));
        $this->assertEquals('abCDE', $closure('a', 'b', 'C', 'D', 'E'));
    }

    function test_getEnvOrSkip()
    {
        $this->assertEquals('value', $this->getEnvOrSkip('THIS_IS_ENV'));
        $this->expectException(SkippedTestError::class);
        $this->getEnvOrSkip('THIS_IS_ENV_EMPTY');
    }

    function test_getConstantOrSkip()
    {
        $this->assertEquals('value', $this->getConstOrSkip('THIS_IS_CONST'));
        $this->expectException(SkippedTestError::class);
        $this->getConstOrSkip('UNDEFINED_CONST');
    }

    function test_getClassMap()
    {
        $classmap = $this->getClassMap();
        $this->assertArrayHasKey(\DeepCopy\TypeFilter\ShallowCopyFilter::class, $classmap);
        $this->assertEquals(realpath(__DIR__ . '/../../vendor/myclabs/deep-copy/src/DeepCopy/TypeFilter/ShallowCopyFilter.php'), $classmap[\DeepCopy\TypeFilter\ShallowCopyFilter::class]);
        $this->assertArrayHasKey(\DeepCopy\TypeFilter\ShallowCopyFilter::class, $classmap);
        $this->assertEquals(realpath(__DIR__ . '/../../vendor/phpunit/phpunit/src/Framework/Constraint/Traversable/ArrayHasKey.php'), $classmap[\PHPUnit\Framework\Constraint\ArrayHasKey::class]);
    }

    function test_getClassByDirectory()
    {
        $psr4 = $this->getClassByDirectory(__DIR__ . '/../../vendor/myclabs/deep-copy/src/DeepCopy/TypeFilter');
        $this->assertContains(\DeepCopy\TypeFilter\ShallowCopyFilter::class, $psr4);
        $this->assertContains(\DeepCopy\TypeFilter\Spl\ArrayObjectFilter::class, $psr4);
        $this->assertNotContains(\DeepCopy\TypeMatcher\TypeMatcher::class, $psr4);

        $classmap = $this->getClassByDirectory(__DIR__ . '/../../vendor/phpunit/phpunit/src/Framework/Constraint');
        $this->assertContains(\PHPUnit\Framework\Constraint\ArrayHasKey::class, $classmap);
        $this->assertContains(\PHPUnit\Framework\Constraint\BinaryOperator::class, $classmap);
        $this->assertNotContains(\PHPUnit\Framework\Assert::class, $classmap);
    }

    function test_getClassByNamespace()
    {
        $psr4 = $this->getClassByNamespace(\DeepCopy\TypeFilter\TypeFilter::class);
        $this->assertContains(\DeepCopy\TypeFilter\TypeFilter::class, $psr4);
        $this->assertContains(\DeepCopy\TypeFilter\Spl\ArrayObjectFilter::class, $psr4);
        $this->assertNotContains(\DeepCopy\TypeMatcher\TypeMatcher::class, $psr4);

        $classmap = $this->getClassByNamespace(\PHPUnit\Framework\Constraint\Constraint::class);
        $this->assertContains(\PHPUnit\Framework\Constraint\ArrayHasKey::class, $classmap);
        $this->assertContains(\PHPUnit\Framework\Constraint\BinaryOperator::class, $classmap);
        $this->assertNotContains(\PHPUnit\Framework\Assert::class, $classmap);
    }

    function test_emptyDirectory()
    {
        $directory = $this->emptyDirectory();
        $this->assertFileExists($directory);

        @mkdir("$directory/dummy");
        touch($dummy = "$directory/dummy/dummy.txt");
        $this->assertFileExists($dummy);

        $directory = $this->emptyDirectory();
        $this->assertFileExists($directory);

        $this->assertFileDoesNotExist($dummy);
    }

    function test_backgroundTask()
    {
        $dir = $this->emptyDirectory();
        $this->backgroundTask(function () use ($dir) {
            while (true) {
                sleep(1);
                file_put_contents("$dir/log.txt", "added\n", FILE_APPEND);
            }
        });

        that("$dir/log.txt")->fileNotExists();

        usleep(1000 * 1500);
        that("$dir/log.txt")->fileEquals("added\n");
    }

    function test_report()
    {
        $this->report('string report');
        $this->report('string report2');
        $this->report(['array', 'report']);
        $this->report(['array', 'report2']);
        $this->assertEquals(1, 1);
    }
}
