<?php

namespace Sputnik\Bundle\CaptchaBundle\Generator;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_Generator
 * @author Dmitri Lakachauskis <dmitri@amparo.lv>
 */
class CaptchaGenerator
{
    private $chars;
    private $length;
    private $code;

    /**
     * @param string  $chars
     * @param integer $length
     */
    public function __construct($chars, $length)
    {
        $this->chars  = $chars;
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        if (null === $this->code) {
            $this->code = $this->generate();
        }
        return $this->code;
    }

    /**
     * @return string
     */
    private function generate()
    {
        $result = '';
        $chars  = str_split($this->chars);

        for ($i = 0; $i < $this->length; $i++) {
            $result .= $chars[array_rand($chars)];
        }

        return $result;
    }
}
