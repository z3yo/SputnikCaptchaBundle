<?php

namespace Sputnik\Bundle\CaptchaBundle\Form\Type;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sputnik\Bundle\CaptchaBundle\Generator\CaptchaGenerator;
use Sputnik\Bundle\CaptchaBundle\Validator\CaptchaValidator;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_Type
 * @author Dmitri Lakachauskis <dmitri@amparo.lv>
 * @author Romuald Bulyshko <romuald@amparo.lv>
 */
class CaptchaType extends AbstractType
{
    private $session;
    private $presets;
    private $fonts;
    private $sessionKey;

    /**
     * @param SessionInterface $session
     * @param array $presets
     * @param array $fonts
     */
    public function __construct(SessionInterface $session, array $presets, array $fonts)
    {
        $this->session = $session;
        $this->presets = $presets;
        $this->fonts   = $fonts;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sputnik_captcha';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'session_key'  => null,
            'preset'       => 'default',
            'font'         => isset($this->presets['default']) ? $this->presets['default']['font'] : null,
            'width'        => null,
            'height'       => null,
            'length'       => null,
            'alphabet'     => null,
            'angle'        => null,
            'color'        => null,
            'format'       => null,
            'bgcolor'      => null,
            'shadow_color' => null,
            'use_shadow'   => null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOptionValues()
    {
        return array(
            'preset' => array_keys($this->presets),
            'font'   => array_keys($this->fonts)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['session_key'])) {
            $this->sessionKey = $options['session_key'];
        } else {
            $this->sessionKey = $builder->getForm()->getName();
        }
        $builder->addValidator(new CaptchaValidator($this->session, $this->sessionKey));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormViewInterface $view, FormInterface $form, array $options)
    {
        // Override preset options
        $params = $this->presets[$options['preset']];
        foreach (array('width', 'height', 'length', 'alphabet', 'font', 'angle', 'color', 'format', 'shadow_color', 'use_shadow') as $option) {
            if (isset($options[$option])) {
                $params[$option] = $options[$option];
            }
        }
        extract($params);

        // Setup generator
        $generator = new CaptchaGenerator($alphabet, $length, $this->fonts[$font]);
        $generator->setColors($color)
                  ->setFormat($format)
                  ->setMaxFontAngle($angle)
                  ->setShadowColor($params['shadow_color'])
                  ->setEnableShadow($params['use_shadow']);
        if ($bgcolor === CaptchaGenerator::COLOR_TRANSPARENT) {
            $generator->setTransparentBackground()->setFormat(CaptchaGenerator::FORMAT_PNG);
        } else {
            $generator->setBackgroundColor($bgcolor);
        }

        $view->setVar('captcha', $generator->getDataSource($width, $height));
        $view->setVar('captcha_width', $width);
        $view->setVar('captcha_height', $height);
        $view->setVar('value', '');

        $this->session->set($this->sessionKey, $generator->getCode());
    }
}
