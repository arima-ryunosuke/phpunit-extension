<?php
/** @noinspection PhpDocSignatureInspection */

namespace ryunosuke\Test;

use ryunosuke\PHPUnit\Annotester;

class AnnotesterTest extends \ryunosuke\Test\AbstractTestCase
{
    function test_all()
    {
        $annotester = new Annotester([
            Example::class => [1],
            '$x'           => 1,
            '$n1'          => 1,
        ]);

        $annotester->test(__NAMESPACE__ . '\\foo');

        $annotester->test(Example::class, 'staticMethod');
        $annotester->test(Example::class, 'instanceMethod');
        $annotester->test(Example::class, 'block');
        $annotester->test(Example::class, 'codeblock');

        $annotester->test('that($x + $n1)->is(2);');
    }

    function test_filter()
    {
        $annotester = new Annotester();

        $this->assertEquals(3, $annotester->test(Misc::class));

        $this->assertEquals(1, $annotester->test(Misc::class, \ReflectionMethod::IS_STATIC));
        $this->assertEquals(1, $annotester->test(Misc::class, \ReflectionMethod::IS_PRIVATE));
        $this->assertEquals(1, $annotester->test(Misc::class, \ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC));
        $this->assertEquals(0, $annotester->test(Misc::class, \ReflectionMethod::IS_PROTECTED));

        $this->assertEquals(1, $annotester->test(Misc::class, 'static*'));
        $this->assertEquals(3, $annotester->test(Misc::class, '*Method'));
        $this->assertEquals(0, $annotester->test(Misc::class, 'hoge'));

        $this->assertEquals(1, $annotester->test(Misc::class, function (\ReflectionMethod $ref) {
            return $ref->hasReturnType();
        }));
    }

    function test_resolve()
    {
        // resolve other variable
        $annotester = new Annotester([
            Example::class => function ($x) { return new Example($x); },
            '$x'           => 1,
        ]);
        $annotester->test(Example::class . '::instanceMethod');

        // resolve other instance
        $annotester = new Annotester([
            Example::class    => [
                'ex'   => function (\Exception $x) { return new Example($x); },
                'zero' => function (...$args) { return new Example(...$args); },
            ],
            \Exception::class => new \Exception(),
        ]);
        $annotester->test(Example::class . '::return');
    }

    function test_options()
    {
        $outdir = sys_get_temp_dir() . '/ptest';
        \ryunosuke\PHPUnit\rm_rf($outdir);
        clearstatcache();

        // outdir option
        $this->assertFileNotExists($outdir);
        new Annotester([], ['outdir' => $outdir]);
        $this->assertFileExists($outdir);

        // format option
        $this->assertFileNotExists("$outdir/ryunosuke/Test/Example.php");
        $this->assertFileNotExists("$outdir/22e1555e78381b6dbffedaf915f00b2e8f721303.php");
        $annotester = new Annotester([Example::class => [1]], ['outdir' => $outdir, 'format' => null]);
        $annotester->test(Example::class . '::instanceMethod');
        $annotester->test('that(1 + 1)->is(2);');
        $this->assertFileExists("$outdir/ryunosuke/Test/Example-instanceMethod.php");
        $this->assertFileExists("$outdir/22e1555e78381b6dbffedaf915f00b2e8f721303.php");
    }
}

/**
 * @that(1, 2)->is(3)
 */
function foo($x, $y)
{
    return $x + $y;
}

/**
 * ```php
 * $this->staticMethod(1, 2)->is(3);
 * $this->instanceMethod(1)->is(2);
 * ```
 */
class Example
{
    private $x;

    public function __construct($x = 0)
    {
        $this->x = $x;
    }

    /**
     * means `that(Example::staticMethod(1, 2))->is(3)`
     * @that(1, 2)->is(3)
     */
    public static function staticMethod($x, $y)
    {
        return $x + $y;
    }

    /**
     * means `that(new Example(1))->instanceMethod(2)->is(3)` (Example is resolved by dependency container)
     * @that(2)->is(3)
     */
    public function instanceMethod($y)
    {
        return $this->x + $y;
    }

    /**
     * @that(1)->is(2);
     * @test aaa {
     *     $that(1)->is(2);   // means `that(new Example(1))->block(1)->is(2)`
     *     $that(2)->is(3);   // means `that(new Example(1))->block(2)->is(3)`
     *     $that($n1)->is(2); // means `that(new Example(1))->block($n1)->is(2)` ($n1 is resolved by dependency container)
     *     $that($x + $n1)->is(3);
     * }
     */
    public function block($y)
    {
        return $this->x + $y;
    }

    /**
     * @that(1)->is(2);
     *
     * ```php
     * $that(1)->is(2);
     * $that(2)->is(3);
     * ```
     *
     * ```php:context
     * $that(3)->is(4);
     * $this->instanceMethod(4)->is(5);
     * ```
     */
    public function codeblock($y)
    {
        return $this->x + $y;
    }

    /**
     * @test ex {
     *     $that()->isInstanceOf(\Throwable::class)
     * }
     * @test zero {
     *     $that()->is(0)
     * }
     */
    public function return()
    {
        return $this->x;
    }
}

class Misc
{
    public function nodoctest() { }

    /**
     * @that(1, 2)->is(3)
     */
    public static function staticMethod($x, $y)
    {
        return $x + $y;
    }

    /**
     * @that(1, 2)->is(3)
     */
    private function privateMethod($x, $y)
    {
        return self::staticMethod($x, $y);
    }

    /**
     * @that(1, 2)->is(3)
     */
    public function publicMethod($x, $y): int
    {
        return $this->privateMethod($x, $y);
    }
}
