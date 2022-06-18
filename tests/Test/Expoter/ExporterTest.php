<?php

namespace ryunosuke\Test\Exporter;

use Exception;
use ryunosuke\PHPUnit\Exporter\Exporter;
use SplObjectStorage;
use stdClass;

class ExporterTest extends \ryunosuke\Test\AbstractTestCase
{
    public static function setUpBeforeClass(): void
    {
        Exporter::insteadOf();
    }

    function test_export()
    {
        $exporter = new Exporter();

        $this->assertSame('null', $exporter->export(null));
        $this->assertSame('true', $exporter->export(true));
        $this->assertSame('false', $exporter->export(false));
        $this->assertSame('3.0', $exporter->export(3.00));
        $this->assertSame("'string'", $exporter->export('string'));
        $this->assertSame('Quoted String: "a\000z"', $exporter->export("a\0z"));

        $fp = tmpfile();
        $this->assertSame("resource(" . intval($fp) . ") of type (stream)", $exporter->export($fp));
        fclose($fp);
        $this->assertSame("resource (closed)", $exporter->export($fp));

        $array = [
            'a' => [
                'b' => [
                    'c' => ['X'],
                ],
            ],
        ];
        $array['R'] = &$array;
        $this->assertSame(<<<EXPECTED
        Array &0 (
            'a' => Array &1 (
                'b' => Array &2 (
                    'c' => Array &3 (
                        0 => 'X'
                    )
                )
            )
            'R' => Array &4 (
                'a' => Array &5 (
                    'b' => Array &6 (
                        'c' => Array &7 (
                            0 => 'X'
                        )
                    )
                )
                'R' => Array &4
            )
        )
        EXPECTED, $exporter->export($array));

        $object = $o1 = (object) [
            'a' => $o2 = (object) [
                'b' => $o3 = (object) [
                    'c' => $o4 = (object) ['X'],
                ],
            ],
        ];
        $object->R = $object;
        $id = fn($v) => spl_object_id($v);
        $this->assertSame(<<<EXPECTED
        stdClass Object &{$id($o1)} (
            'a' => stdClass Object &{$id($o2)} (
                'b' => stdClass Object &{$id($o3)} (
                    'c' => stdClass Object &{$id($o4)} (
                        0 => 'X'
                    )
                )
            )
            'R' => stdClass Object &{$id($o1)}
        )
        EXPECTED, $exporter->export($object));
    }

    function test_shortenedExport()
    {
        $exporter = new Exporter();

        $this->assertSame('null', $exporter->shortenedExport(null));
        $this->assertSame('true', $exporter->shortenedExport(true));
        $this->assertSame('false', $exporter->shortenedExport(false));

        $n = '１２３４５６７８９';
        $this->assertSame("'１２３４５６７８９Ａ１２３４５６７８９...３４５６７８９Ｅ１２３４５６７８９Ｆ'", $exporter->shortenedExport("{$n}Ａ{$n}Ｂ{$n}Ｃ{$n}Ｄ{$n}Ｅ{$n}Ｆ"));
        $n = '123456789';
        $this->assertSame("'123456789A123456789B123456789C12345678...456789F123456789G123456789H123456789I'", $exporter->shortenedExport("{$n}A{$n}B{$n}C{$n}D{$n}E{$n}F{$n}G{$n}H{$n}I"));

        $this->assertSame("Array (...)", $exporter->shortenedExport(['a' => 'b']));
        $this->assertSame("stdClass Object (...)", $exporter->shortenedExport((object) ['a' => 'b']));
    }

    function test_shortenedRecursiveExport()
    {
        $exporter = new Exporter();

        $array = [
            'a' => [
                'b' => [
                    'c' => ['X'],
                ],
            ],
        ];
        $array['R'] = &$array;
        $this->assertSame("array(array(array('X'))), *RECURSION*", $exporter->shortenedRecursiveExport($array));
    }

    /** @noinspection PhpUnusedPrivateFieldInspection */
    function test_toArray()
    {
        $exporter = new Exporter();

        $this->assertSame([], $exporter->toArray(null));
        $this->assertSame(['string'], $exporter->toArray('string'));
        $this->assertSame(['array'], $exporter->toArray(['array']));

        $actual = $exporter->toArray(new class extends Exception {
            private $privatePorperty = 123;
        });
        $this->assertArrayHasKey('file', $actual);
        $this->assertArrayHasKey('line', $actual);
        unset($actual['file'], $actual['line']);
        $this->assertSame([
            'privatePorperty' => 123,
            'message'         => '',
            'string'          => '',
            'code'            => 0,
            'previous'        => null,
        ], $actual);

        $storage = new SplObjectStorage();
        $storage->attach($o1 = new stdClass(), 'std1');
        $storage->attach($o2 = new stdClass(), 'std2');
        $actual = $exporter->toArray($storage);
        $this->assertSame([
            spl_object_hash($o1) => [
                'obj' => $o1,
                'inf' => 'std1',
            ],
            spl_object_hash($o2) => [
                'obj' => $o2,
                'inf' => 'std2',
            ],
        ], $actual);
    }
}
