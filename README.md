# PHPUnit Fluent Assertions

## Description

This package adds phpunit Fluent interface. 

- e.g. `actual('xxx')->isEqual('xxx')`
- e.g. `actual(1)->isInt()->isBetween(1, 9)`
- e.g. `actual('qwe asd zxc')->stringStartsWith('qwe')->stringEndsWith('zxc')`

## Install

```json
{
    "require-dev": {
        "ryunosuke/phpunit-extension": "dev-master"
    }
}
```

## Usage

```php
// e.g. bootstrap.php
function actual($actual)
{
    return new \ryunosuke\PHPUnit\Actual($actual);
}

// TestCase
class ExampleTest extends \PHPUnit\Framework\TestCase
{
    function test()
    {
        # fluent interface
        // means: assertThat(5, logicalAnd(isType('int'), greaterThanOrEqual(1), lessThanOrEqual(9)));
        actual(5)->isInt()->isBetween(1, 9);

        # "all*" asserts per values (assert AND all values)
        // means: assertThat(1, greaterThan(0)); assertThat(2, greaterThan(0)); assertThat(3, greaterThan(0));
        actual([1, 2, 3])->allGreaterThan(0);

        # "*Any" asserts multiple arguments (assert OR all arguments)
        // means: assertThat('x', logicalOr(equalTo('x'), equalTo('y'), equalTo('z')));
        actual('x')->isEqualAny(['x', 'y', 'z']);
        // ignore case (other arguments are normal)
        actual('X')->isEqualAny(['x', 'y', 'z'], 0, 0.0, false, true);

        # "*All" asserts multiple arguments (assert AND all arguments)
        // means: assertThat(['x' => 'X', 'y' => 'Y'], logicalAnd(arrayHasKey('x'), arrayHasKey('y')));
        actual(['x' => 'X', 'y' => 'Y'])->arrayHasKeyAll(['x', 'y']);

        # "assert" asserts directly constraint (multiple arguments OR all arguments)
        // means: assertThat('x', equalTo('x'));
        actual('x')->assert(equalTo('x'));
        // means: assertThat('x', logicalOr(equalTo('x'), equalTo('y'), equalTo('z')));
        actual('x')->assert(equalTo('x'), equalTo('y'), equalTo('z'));

        # "message" describes failure text
        // means: assertThat('x', equalTo('notX'), 'this is failure message');
        actual('x')->message('this is failure message')->isEqual('notX');

        # "catch" catches Throwable (message or code or classname)
        $object = new \ReflectionClass('stdClass');
        actual($object)->catch('Property dummy does not exist')->getProperty('dummy');
        // or Exception class name
        actual($object)->catch(\ReflectionException::class)->getProperty('dummy');
        // or Exception instance (assert message and code and classname)
        actual($object)->catch(new \ReflectionException('Property dummy does not exist', 0))->getProperty('dummy');

        # "try" is not thrown method call and actual
        $object = new \ReflectionObject((object) ['x' => 'X']);
        // returns original result and actual if not thrown
        actual($object)->try('getProperty', 'x')->isInstanceOf(\ReflectionProperty::class);
        // returns thrown exception and actual if thrown
        actual($object)->try('getProperty', 'y')->isInstanceOf(\ReflectionException::class);

        # array access returns array's value and actual
        $array = ['x' => 'X'];
        // means: assertThat($array['x'], equalTo('X'));
        actual($array)['x']->isEqual('X');

        # property access returns property and actual (non-public access is possible)
        $object = (object) ['x' => 'X'];
        // means: assertThat($object->x, equalTo('X'));
        actual($object)->x->isEqual('X');

        # method call returns original result and actual (non-public access is possible)
        $object = new \ArrayObject([1, 2, 3]);
        // means: assertThat($object->getArrayCopy(), equalTo([1, 2, 3]));
        actual($object)->getArrayCopy()->isEqual([1, 2, 3]);

        # actual's method prefers to original method
        $object = new \ArrayObject([1, 2, 3]);
        // means: assertThat($object, countOf(3)); not: $object->count();
        actual($object)->count(3);
        // "call" invokes original method
        actual($object)->call('count')->isEqual(3);

        # "__invoke" returns original::__invoke and actual
        $object = function ($a, $b) { return $a + $b; };
        // means: assertThat($object(1, 2), equalTo(3));
        actual($object)(1, 2)->isEqual(3);

        # "parent" backs to before value (like a jQuery `end`)
        $object = new \ArrayObject(['x' => 'X', 'y' => 'Y'], \ArrayObject::ARRAY_AS_PROPS);
        // means: assertThat($object->x, equalTo('X')); assertThat($object->y, equalTo('Y')); assertThat($object, isInstanceOf(\ArrayObject::class));
        actual($object)
            ['x']->isEqual('X')->parent()
            ->y->isEqual('Y')->parent()
            ->isInstanceOf(\ArrayObject::class);

        # autoback calls parent automatically after assertion (parent is needless)
        $object = new \ArrayObject(['x' => 'X', 'y' => 'Y'], \ArrayObject::ARRAY_AS_PROPS);
        actual($object)->autoback()
            ['x']->isEqual('X')
            ->y->isEqual('Y')
            ->getArrayCopy()->isEqual(['x' => 'X', 'y' => 'Y'])
            ->getIterator()->isInstanceOf(\ArrayIterator::class)
            ->count(2);
    }
}
```

### Custom constraints

Internals:

| constraint         | description
| :---               | :---
| FileContains       | assert file contains string
| FileEquals         | assert file equals string
| IsBetween          | assert range of number
| IsBlank            | assert blank string
| IsCType            | assert value by ctype_xxx
| IsEqualFile        | assert string equals file
| IsEqualIgnoreWS    | assert string ignoring whitespace
| IsFalsy            | assert value like a false
| IsTruthy           | assert value like a true
| IsValid            | assert value by filter_var
| StringLength       | assert length of string
| Throws             | assert callable should throw exception

Alias:

`\ryunosuke\PHPUnit\Actual::$constraintVariations` is searching for variation from other constraint.

```php
// Alias. This ables to use: $actual->isSame('other')
\ryunosuke\PHPUnit\Actual::$constraintVariations['isSame'] = IsIdentical::class;
// Construct. This ables to use: $actual->isFoo()
\ryunosuke\PHPUnit\Actual::$constraintVariations['isArray'] = [IsType::class => [IsType::TYPE_ARRAY]];
// Mix. This ables to use: $actual->isNullOrString()
\ryunosuke\PHPUnit\Actual::$constraintVariations['isNullOrString'] = [IsNull::class, IsType::class => [IsType::TYPE_STRING]];
// Instance. This ables to use: $actual->lineCount(5)
\ryunosuke\PHPUnit\Actual::$constraintVariations['lineCount'] = new class(/* this argument is dummy */0) extends \PHPUnit\Framework\Constraint\Constraint
{
    private $lineCount;

    public function __construct(int $lineCount)
    {
        $this->lineCount = $lineCount;
    }

    protected function matches($other): bool
    {
        return $this->lineCount === (preg_match_all("#\\R#", $other) + 1);
    }

    public function toString(): string
    {
        return 'is ' . $this->lineCount . ' lines';
    }
};
```

User defined:

`\ryunosuke\PHPUnit\Actual::$constraintNamespaces` is searching for constraint namespace.

```php
// This ables to use: $actual->yourConstraint()
\ryunosuke\PHPUnit\Actual::$constraintNamespaces['your namespace'] = 'your constraint directory';
```

### Code completion

Actual class is using `\ryunosuke\PHPUnit\Annotation` trait.
This trait is not declared by default. If declare this class in your project space, then custom method and code completion are enabled.

```php
// e.g. bootstrap.php
namespace ryunosuke\PHPUnit {
    /**
     * @method \ryunosuke\PHPUnit\Actual isHoge()
     */
    trait Annotation
    {
        function isFuga(): \ryunosuke\PHPUnit\Actual {
        {
            $this->assert(new \PHPUnit\Framework\Constraint\IsEqual('fuga'));
            return $this;
        }
    }
}
```

That ables to use `$actual->isH(oge)` completion and `$actual->isF(uga)` method.

Or call `\ryunosuke\PHPUnit\Actual::generateAnnotation`.
This method returns annotation via `$constraintVariations` and `$constraintNamespaces`.

### IDE helper

`bin/phpunit-current` is IDE helper. See [bin/phpunit-current](bin/phpunit-current).

- Run test project context (php verion, phpunit version, configuration etc)
- Run test editing class/method
- Run latest test
- Switching coverage

But this is very legacy. Better to use phpstorm Test Runner.

## Release

Versioning is Semantic Versioning.

### 0.1.0

- [feature] add "*All" method
- [feature] add "try" method
- [feature] add "message" method
- [feature] add "__invoke" method
- [feature] add "file*" constraint
- [feature] replace with original "logical*" constraint
- [feature] variation adds "is" alias
- [feature] variation supports anonymouse class
- [fixbug] variation ignores arguments
- [change] __get/__call can access no-public member

### 0.0.0

- publish

## License

MIT
