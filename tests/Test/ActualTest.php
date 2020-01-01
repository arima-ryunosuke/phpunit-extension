<?php

namespace ryunosuke\Test;

use PHPUnit\Framework\Constraint\IsEqual;
use ryunosuke\PHPUnit\Actual;

class ActualTest extends \ryunosuke\Test\AbstractTestCase
{
    function actual($actual)
    {
        return new Actual($actual);
    }

    function test_generateAnnotation()
    {
        $annotations = Actual::generateAnnotation(false);
        $this->assertIsString($annotations);
        $this->assertStringContainsString('@method', $annotations);
        $this->assertStringContainsString('@see', $annotations);
        $this->assertStringContainsString('isFalse', $annotations);
        $this->assertStringContainsString('isNotFalse', $annotations);
        $this->assertStringContainsString('stringLength', $annotations);
        $this->assertStringContainsString('notStringLength', $annotations);
        $this->assertStringContainsString(' all', $annotations);
        $this->assertStringContainsString('Any(', $annotations);
        $this->assertStringContainsString('isNullOrString()', $annotations);
        $this->assertStringContainsString('gte($value)', $annotations);
        $this->assertStringContainsString('equalsCanonicalizing($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = true, bool $ignoreCase = false)', $annotations);

        $this->assertIsArray(Actual::generateAnnotation(true));
    }

    function test_ok()
    {
        $this->actual('qweN N Nzxc')
            ->isNullOrString()
            ->matches('#^[a-z\s]+$#i')
            ->stringContains(' N ')
            ->stringStartsWith('qwe')
            ->stringEndsWith('zxc')
            ->notStringStartsWith('zxc')
            ->isEqualIgnoreWS('
qweN
N
Nzxc ');

        $this->actual(2)
            ->isInt()
            ->isEqual(2)
            ->gt(1)
            ->gte(2)
            ->lt(3)
            ->lte(2)
            ->isBetween(1, 3);

        $ex = new \Exception();
        $this->actual($ex)
            ->isSame($ex)
            ->isNotSame(new \Exception())
            ->isInstanceOf(\Exception::class)
            ->isInstanceOf(\Throwable::class)
            ->isNotInstanceOf(\ArrayObject::class);

        $this->actual(function () { throw new \Exception('hogera', 22); })->throws('hogera');
        $this->actual(function () { throw new \Exception('hogera', 22); })->throws(new \Exception('hogera', 22));

        $this->actual('qwe')
            ->stringLengthAny([1, 2, 3])
            ->isEqualAny(['qwe', 'asd'])
            ->isEqualAny(['QWE', 'asd'], 0, 10, false, true);

        $this->actual([true, 1, '1', new \stdClass()])->allIsTruthy();
        $this->actual(['1', '2', '3'])->allIsString();
    }

    function test_ng()
    {
        $this->ng(function () {
            $this->actual('qweN N Nzxc')->stringStartsWith('hoge');
        }, 'starts with "hoge".');

        $this->ng(function () {
            $this->actual(9)->lessThan(5);
        }, '9 is less than 5.');

        $this->ng(function () {
            $this->actual(new \Exception())->isInstanceOf(\ArrayObject::class);
        }, 'is instance of class "ArrayObject"');

        $this->ng(function () {
            $this->actual(function () { })->throws('dummy');
        }, "'dummy', 0");

        $this->ng(function () {
            $this->actual(function () { throw new \Exception('hogera', 22); })->throws('dummy');
        }, "'dummy', 0");

        $this->ng(function () {
            $this->actual(function () { throw new \Exception('hogera', 22); })->throws(new \Exception('hogera', 99));
        }, "'hogera', 99");

        $this->ng(function () {
            $this->actual('qwe')->isEqualAny(['QWE', 'asd', 'zxc']);
        }, "'qwe' is equal to 'QWE' or is equal to 'asd' or is equal to 'zxc'");

        $this->ng(function () {
            $this->actual([true, 1, 0, new \stdClass()])->allIsTruthy();
        }, "0 is Truthy");

        $this->ng(function () {
            $this->actual(['1', 2, '3'])->allIsString();
        }, '2 is of type "string"');
    }

    function test_accessor()
    {
        $actual = $this->actual((object) [
            'x' => 'X',
        ]);
        $actual->x->isEqual('X');

        $actual = $this->actual(new \ArrayObject([
            'x' => 'X',
            'y' => 'Y',
        ], \ArrayObject::ARRAY_AS_PROPS));

        $actual['x']->isEqual('X');
        $actual->y->isEqual('Y');

        $this->ng(function () use ($actual) {
            $actual['undefined']->isInt();
        }, "has the key 'undefined'");

        $this->ng(function () use ($actual) {
            $actual->undefined->isInt();
        }, 'has attribute "undefined"');
    }

    function test_call()
    {
        $actual = $this->actual(new class
        {
            function method($x)
            {
                if ($x === null) {
                    throw new \Exception('this is message.', 123);
                }
                return $x * 10;
            }
        });

        /** @noinspection PhpUndefinedMethodInspection */
        {
            $actual->method(3)->isEqual(30);
            $actual->call('method', 5)->isEqual(50);
            $actual->catch(new \Exception('this is message.', 123))->method(null);
            $actual->catch(new \Exception('this is message.', 123))->call('method', null);
            $actual->isType('object');
        }

        $actual = $this->actual(
        /**
         * @method hoge()
         * @method int fuga()
         */
            new class()
            {
                public function __call($name, $arguments)
                {
                    return $name . $arguments[0] * 10;
                }
            });
        /** @noinspection PhpUndefinedMethodInspection */
        {
            $actual->hoge(3)->isEqual('hoge30');
            $actual->fuga(4)->isEqual('fuga40');
            $this->ng(function () use ($actual) {
                /** @noinspection PhpUndefinedMethodInspection */
                $actual->piyo();
            }, 'piyo');
        }

        $actual = $this->actual(new \stdClass());
        $this->ng(function () use ($actual) {
            /** @noinspection PhpUndefinedMethodInspection */
            $actual->undefinedMethod();
        }, 'undefinedMethod');

        $this->ng(function () {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->actual(null)->undefinedMethod();
        }, "undefinedMethod");
    }

    function test_parent()
    {
        $actual = $this->actual(new \ArrayObject([
            'x' => 'X',
            'y' => 'Y',
            'z' => new \ArrayObject([
                'a' => 'A',
                'b' => 'B',
            ], \ArrayObject::ARRAY_AS_PROPS),
        ], \ArrayObject::ARRAY_AS_PROPS));

        $actual['z']->count(2)
            ->a->isEqual('A')->parent()
            ->b->isEqual('B')->parent(2)
            ->x->isEqual('X')->parent()
            ->y->isEqual('Y')->parent(99)
            ->call('count')->isEqual(3)->parent()
            ->isInstanceOf(\ArrayObject::class);
    }

    function test_parent_autoback()
    {
        $actual = $this->actual(new class
        {
            function getA() { return 'A'; }

            function getB() { return 'B'; }

            function getC() { return 'C'; }
        });

        /** @noinspection PhpUndefinedMethodInspection */
        $actual->autoback()
            ->getA()->isEqual('A')
            ->getB()->isEqual('B')
            ->getC()->isEqual('C');
    }

    function test_assert()
    {
        $this->actual('qwe')->assert(new IsEqual('qwe'));
        $this->actual('qwe')->assert(new IsEqual('asd'), new IsEqual('qwe'));

        $this->ng(function () {
            $this->actual('qwe')->assert(new IsEqual('asd'), new IsEqual('zxc'));
        }, "'qwe' is equal to 'asd' or is equal to 'zxc'");
    }

    function test_catch()
    {
        $thrower = new class()
        {
            function throw() { throw new \Exception('actual message', 123); }

            function nothrow() { }
        };

        /** @noinspection PhpUndefinedMethodInspection */
        $this->actual($thrower)->catch('actual message')->throw();
        $this->ng(function () use ($thrower) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->actual($thrower)->catch('actual message')->nothrow();
        }, "should throw");
    }

    function test_variation()
    {
        $this->actual('hoge')->isHoge();

        $this->actual('hoge')->isSame('hoge');
        $this->actual(['a', 'b'])->equalsCanonicalizing(['b', 'a']);
        $this->actual('hoge')->equalsIgnoreCase('HOGE');
        $this->actual('hoge')->matches('#hoge#');
        $this->actual(5)->gt(4)->gte(5);
        $this->actual(5)->lt(6)->lte(5);
        $this->actual(null)->isNullOrString();

        $this->actual([12])->isArray();
        $this->actual(true)->isBool();
        $this->actual(1.23)->isFloat();
        $this->actual(1234)->isInt();
        $this->actual(null)->isNull();
        $this->actual("12")->isNumeric();
        $this->actual($this)->isObject();
        $this->actual(STDIN)->isResource();
        $this->actual("str")->isString();
        $this->actual(12345)->isScalar();
        $this->actual(function () { })->isCallable();
        $this->actual(new \ArrayObject())->isIterable();

        $this->actual("Az")->isCtypeAlnum();
        $this->actual("Az")->isCtypeAlpha();
        $this->actual("\n")->isCtypeCntrl();
        $this->actual("12")->isCtypeDigit();
        $this->actual("Az")->isCtypeGraph();
        $this->actual("az")->isCtypeLower();
        $this->actual("az")->isCtypePrint();
        $this->actual("()")->isCtypePunct();
        $this->actual("  ")->isCtypeSpace();
        $this->actual("AZ")->isCtypeUpper();
        $this->actual("Ff")->isCtypeXdigit();

        $this->actual("hoge@example.com")->isValidEmail();
        $this->actual("127.0.0.1")->isValidIp();
        $this->actual("127.0.0.1")->isValidIpv4();
        $this->actual("::1")->isValidIpv6();
        $this->actual("00-00-5E-00-53-00")->isValidMac();
        $this->actual("http://example.com")->isValidUrl();
    }
}
