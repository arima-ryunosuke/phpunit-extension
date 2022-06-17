<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\HtmlMatchesArray;

class HtmlMatchesArrayTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new HtmlMatchesArray([
            'div' => [
                'text1',
                'text2',
                'id'       => 'hoge',
                'hidden'   => true,
                'disabled' => false,
                'span'     => [
                    'span1',
                    'span2',
                ],
            ],
        ]);
        $this->assertTrue($constraint->evaluate('<div id="hoge" hidden>text1 text2<span>span1 span2</span></div>', '', true));
        $this->assertTrue($constraint->evaluate('<div id="hoge" hidden=valid>text1 text2 text3<span><span>span1</span> span2 span3</span></div>', '', true));
        $this->assertTrue($constraint->evaluate('<hoge></hoge><div id="hoge" hidden=valid>text1 text2 text3<span><span>span1</span> span2 span3</span></div>', '', true));
        $this->assertFalse($constraint->evaluate('<div></div><div></div>', '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('<div></div><div></div>');
        }, 'div should be single element. found 2 elements');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('<div id="fuga" hidden>text1 text2<span>span1 span2</span></div>');
        }, 'div[id] should be "hoge"');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('<div id="hoge" hidden2>text1 text2<span>span1 span2</span></div>');
        }, 'div[hidden] should exist');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('<div id="hoge" hidden disabled>text1 text2<span>span1 span2</span></div>');
        }, 'div[disabled] should not exist');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('<div id="hoge" hidden>hoge1 text2<span>span1 span2</span></div>');
        }, 'div textContent contains "text1"');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('<div id="hoge" hidden>text1 text2<span>span1 hoge</span></div>');
        }, 'div/span textContent contains "span2"');
    }

    function test_assert()
    {
        that('<label for="radio-id"><input type="radio" id="radio-id" name="radio-name" value="1" checked>labeltext</label>')->htmlMatchesArray([
            'label' => [
                'for'   => 'radio-id',
                'input' => [
                    'type'    => 'radio',
                    'id'      => 'radio-id',
                    'name'    => 'radio-name',
                    'value'   => '1',
                    'checked' => true,
                ],
                'labeltext',
            ],
        ]);
    }
}