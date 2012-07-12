<?php

namespace Sputnik\Bundle\CaptchaBundle\Generator;

use Imagine\Filter\Transformation;
use Imagine\Image\Point;
use Imagine\Image\Color;
use Imagine\Image\Box;
use Imagine\Gd\Font;
use Imagine\Gd\Imagine;
use Sputnik\Bundle\CaptchaBundle\Imagine\Filter\WaveFilter;
use Sputnik\Bundle\CaptchaBundle\Imagine\Filter\BlurFilter;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_Generator
 */
class CaptchaGenerator
{
    const QUALITY_LOW       = 1;
    const QUALITY_NORMAL    = 2;
    const QUALITY_HIGH      = 3;
    const FORMAT_PNG        = 'png';
    const FORMAT_JPEG       = 'jpeg';
    const COLOR_TRANSPARENT = 'transparent';

    private $chars;
    private $length;
    private $fontInfo;
    private $code;
    private $backgroundColor;
    private $shadowColor;
    private $colors  = array();
    private $angle   = 0;
    private $quality = self::QUALITY_NORMAL;
    private $format  = self::FORMAT_PNG;
    private $shadow  = true;

    /**
     * @param string  $chars
     * @param integer $length
     * @param array   $fontInfo
     */
    public function __construct($chars, $length, array $fontInfo)
    {
        $this->chars    = $chars;
        $this->length   = $length;
        $this->fontInfo = $fontInfo;
    }

    /**
     * @return array
     */
    public static function getFormats()
    {
        return array(self::FORMAT_JPEG, self::FORMAT_PNG);
    }

    /**
     * @param boolean $flag
     *
     * @return CaptchaGenerator
     */
    public function setEnableShadow($flag = true)
    {
        $this->shadow = $flag;

        return $this;
    }

    /**
     * @param integer $quality
     *
     * @return CaptchaGenerator
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return CaptchaGenerator
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param integer $angle
     *
     * @return CaptchaGenerator
     */
    public function setMaxFontAngle($angle)
    {
        $this->angle = $angle;

        return $this;
    }

    /**
     * @param string|array $colors
     *
     * @return CaptchaGenerator
     */
    public function setColors($colors)
    {
        $this->colors = array();
        foreach ((array) $colors as $color) {
            $this->addColor($color);
        }

        return $this;
    }

    /**
     * @param string  $color
     * @param integer $alpha
     *
     * @return CaptchaGenerator
     */
    public function addColor($color, $alpha = 0)
    {
        $this->colors[] = new Color($color, $alpha);

        return $this;
    }

    /**
     * @param string  $color
     * @param integer $alpha
     *
     * @return CaptchaGenerator
     */
    public function setShadowColor($color, $alpha = 0)
    {
        $this->shadowColor = new Color($color, $alpha);

        return $this;
    }

    /**
     * @param string  $color
     * @param integer $alpha
     *
     * @return CaptchaGenerator
     */
    public function setBackgroundColor($color, $alpha = 0)
    {
        $this->backgroundColor = new Color($color, $alpha);

        return $this;
    }

    /**
     * @return CaptchaGenerator
     */
    public function setTransparentBackground()
    {
        return $this->setBackgroundColor('fff', 100);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        if (null === $this->code) {
            $this->code = $this->generateCode();
        }

        return $this->code;
    }

    /**
     * @param integer $width
     * @param integer $height
     *
     * @return string
     */
    public function getDataSource($width, $height)
    {
        return 'data:image/' . $this->format . ';base64,' . base64_encode($this->generate($width, $height));
    }

    /**
     * @return string
     */
    private function generateCode()
    {
        $result = '';
        $chars  = str_split($this->chars);

        for ($i = 0; $i < $this->length; $i++) {
            $result .= $chars[array_rand($chars)];
        }

        return $result;
    }

    /**
     * @param integer $width
     * @param integer $height
     *
     * @return string Binary data
     */
    private function generate($width, $height)
    {
        // Default colors
        if (null === $this->backgroundColor) {
            $this->setBackgroundColor('fff');
        }
        if (!count($this->colors)) {
            $this->setColors('000');
        }

        // Create image
        $imagine = new Imagine;
        $canvas  = new Box($width, $height);
        $image   = $imagine->create($canvas->scale($this->quality), $this->backgroundColor);

        // Drawing tools
        $drawing = $image->draw();
        $letters = str_split($this->getCode());

        $x = 20 * $this->quality;
        foreach ($letters as $letter) {

            // Letter params
            $angle = rand($this->angle * -1, $this->angle);
            $size  = rand($this->fontInfo['min_size'], $this->fontInfo['max_size']) * $this->quality;
            $color = $this->colors[array_rand($this->colors)];

            // Font data
            $font   = new Font($this->fontInfo['file'], $size, $color);
            $coords = $font->box($letter, $angle);

            // Text position
            $y     = round(($height * $this->quality - $coords->getHeight()) / 2);
            $point = new Point($x, $y);

            // Draw shadow
            if ($this->shadow) {
                $shadowFont = new Font($this->fontInfo['file'], $size, $this->shadowColor);
                $drawing->text($letter, $shadowFont, $point->move($this->quality), $angle);
            }

            // Draw letter
            $drawing->text($letter, $font, $point, $angle);

            // Advance
            $x = $x + $coords->getWidth() + $this->fontInfo['spacing'] * $this->quality;
        }

        $transform = new Transformation($imagine);
        $transform->add(new BlurFilter())
                  ->add(new WaveFilter())
                  ->apply($image);

        if ($image->getSize()->getWidth() !== $width) {
            $image->resize(new Box($width, $height));
        }

        return $image->get($this->format);
    }
}
