<?php

namespace Sputnik\Bundle\CaptchaBundle\Imagine\Filter;

use Imagine\Image\ImageInterface;
use Imagine\Filter\FilterInterface;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_Imagine
 * @subpackage Filter
 */
class WaveFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image;
    }
}
