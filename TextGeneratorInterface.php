<?php

/*
 * This file is part of the TextGenerator package.
 *
 * (c) Konstantin Osipov <k.osipov.msk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liderman\TextGenerator;

/**
 * Interface TextGeneratorInterface.
 *
 * @package Liderman\TextGenerator
 */
interface TextGeneratorInterface
{
    /**
     * Generates and returns a new text.
     *
     * Use the rules for generating a plurality of texts
     * Example mask: `Good {morning|day}!`
     *
     * @param string $text Text template
     *
     * @return string Result text
     */
    public function generate($text);
}