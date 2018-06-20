<?php

/*
 * This file is part of the TextGenerator package.
 *
 * (c) Konstantin Osipov <k.osipov.msk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liderman\TextGenerator\Tests;

use Liderman\TextGenerator\TextGenerator;

class TextGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerator()
    {
        $textGen = new TextGenerator();

        $this->assertEquals(
            $textGen->generate("{aaa|aaa}"),
            'aaa'
        );

        $this->assertEquals(
            $textGen->generate("{aaa|aaa} {bbb|bbb}"),
            'aaa bbb'
        );

        $this->assertEquals(
            $textGen->generate("aaa {bbb|bbb} ccc {ddd|ddd} eee"),
            'aaa bbb ccc ddd eee'
        );

        $this->assertEquals(
            $textGen->generate("{{aaa|aaa}|{aaa|aaa}}"),
            'aaa'
        );

        $this->assertEquals(
            $textGen->generate("aaa{bbb{ccc|ccc}ddd|bbb{ccc|ccc}ddd}eee"),
            'aaabbbcccdddeee'
        );

        $this->assertEquals(
            $textGen->generate("aaa"),
            'aaa'
        );

        $this->assertEquals(
            $textGen->generate("{}"),
            ''
        );

        $this->assertEquals(
            $textGen->generate("{|}"),
            ''
        );

        for ($i = 0; $i < 100; $i++) {
            $this->assertContains(
                $textGen->generate("{aaa {bbb|bbb|bbb}|aaa|aaa}, {ccc|ccc}! {ddd|ddd}?"),
                [
                    'aaa, ccc! ddd?',
                    'aaa bbb, ccc! ddd?',
                ]
            );
        }
    }

    public function testCustomConfig()
    {
        $textGen = new TextGenerator();
        $textGen->setStartTag('[');
        $textGen->setEndTag(']');
        $textGen->setSeparator('!');

        $this->assertEquals(
            $textGen->generate("[aaa!aaa]"),
            'aaa'
        );
    }

    public function testConstructor()
    {
        $textGen = new TextGenerator('<', '>', '!');

        $this->assertEquals(
            $textGen->generate("<aaa!aaa>"),
            'aaa'
        );
    }
}