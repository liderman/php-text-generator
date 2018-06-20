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
 * Fast SEO text generator on a mask.
 *
 * @package Liderman\TextGenerator
 */
class TextGenerator implements TextGeneratorInterface
{
    /**
     * Default start tag
     */
    const DEF_START_TAG = '{';

    /**
     * Default end tag
     */
    const DEF_END_TAG = '}';

    /**
     * Default separator
     */
    const DEF_SEPARATOR = '|';

    /**
     * @var string Start tag
     */
    private $startTag;

    /**
     * @var string End tag
     */
    private $endTag;

    /**
     * @var string Separator
     */
    private $separator;

    /**
     * @var string Encoding
     */
    private $encoding = 'UTF-8';

    /**
     * Constructor.
     *
     * @param string $startTag Start tag
     * @param string $endTag End tag
     * @param string $separator Separator
     */
    public function __construct(
        $startTag = self::DEF_START_TAG,
        $endTag = self::DEF_END_TAG,
        $separator = self::DEF_SEPARATOR
    ) {
        $this->setStartTag($startTag);
        $this->setEndTag($endTag);
        $this->setSeparator($separator);
    }

    /**
     * Set start tag.
     *
     * @param string $startTag Start tag
     */
    public function setStartTag($startTag = self::DEF_START_TAG)
    {
        $this->startTag = $startTag;
    }

    /**
     * Set end tag.
     *
     * @param string $endTag End tag
     */
    public function setEndTag($endTag = self::DEF_END_TAG)
    {
        $this->endTag = $endTag;
    }

    /**
     * Set separator.
     *
     * @param string $separator Separator
     */
    public function setSeparator($separator = self::DEF_SEPARATOR)
    {
        $this->separator = $separator;
    }

    /**
     * Set encoding.
     *
     * @param string $encoding Encoding
     */
    public function setEncoding($encoding = 'UTF-8')
    {
        $this->encoding = $encoding;
    }

    /**
     * @inheritdoc
     */
    public function generate($text)
    {
        $startSafePos = 0;
        $startPos = 0;
        $endPos = 0;
        $openLevel = 0;
        $isFind = false;
        $result = '';
        $textLen = mb_strlen($text, $this->encoding);
        for ($i = 0; $i < $textLen; $i++) {
            if (mb_substr($text, $i, 1, $this->encoding) === $this->startTag) {
                if ($openLevel === 0) {
                    $startPos = $i;
                    $result .= mb_substr($text, $startSafePos, $startPos - $startSafePos, $this->encoding);
                }

                $openLevel++;
                continue;
            }

            if (mb_substr($text, $i, 1, $this->encoding) === $this->endTag) {
                $openLevel--;

                if ($openLevel === 0) {
                    $isFind = true;
                    $endPos = $i;

                    $startSafePos = $i + 1;

                    $result .= $this->generate(
                        $this->getRandomPart(
                            mb_substr($text, $startPos + 1, $endPos - ($startPos + 1), $this->encoding)
                        )
                    );

                    continue;
                }
            }
        }

        if ($isFind === false) {
            return $text;
        }

        return $result . mb_substr($text, $endPos + 1, null, $this->encoding);
    }

    /**
     * Returns a random part of text.
     *
     * @param string $text Text
     *
     * @return string Random part of text
     */
    private function getRandomPart($text)
    {
        $openLevel = 0;
        $lastPos = 0;
        $isIgnore = false;
        $parts = array();
        $textLen = mb_strlen($text, $this->encoding);
        for ($i = 0; $i < $textLen; $i++) {
            $currentChar = mb_substr($text, $i, 1, $this->encoding);
            if ($currentChar === $this->startTag) {
                $openLevel++;
                $isIgnore = true;
                continue;
            }

            if ($currentChar === $this->endTag) {
                $openLevel--;
                if ($openLevel === 0) {
                    $isIgnore = false;
                }

                continue;
            }

            if ($isIgnore === true) {
                continue;
            }

            if ($currentChar === $this->separator) {
                $parts[] = mb_substr($text, $lastPos, ($i - $lastPos), $this->encoding);
                $lastPos = $i + 1;
            }
        }

        $parts[] = mb_substr($text, $lastPos, null, $this->encoding);

        return $parts[array_rand($parts)];
    }
}
