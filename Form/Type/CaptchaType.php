<?php

namespace Sputnik\Bundle\CaptchaBundle\Form\Type;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sputnik\Bundle\CaptchaBundle\Generator\CaptchaGenerator;
use Sputnik\Bundle\CaptchaBundle\Validator\CaptchaValidator;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_Type
 */
class CaptchaType extends AbstractType
{
    private $session;
    private $presets;
    private $fonts;
    private $sessionKey;

    /**
     * @param SessionInterface $session
     * @param array            $presets
     * @param array            $fonts
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $presets = $this->presets;
        $resolver->setDefaults(array(
            'session_key'  => null,
            'preset'       => 'default',
            'font'         => function(Options $options) use ($presets) { return $presets[$options['preset']]['font']; },
            'width'        => function(Options $options) use ($presets) { return $presets[$options['preset']]['width']; },
            'height'       => function(Options $options) use ($presets) { return $presets[$options['preset']]['height']; },
            'length'       => function(Options $options) use ($presets) { return $presets[$options['preset']]['length']; },
            'alphabet'     => function(Options $options) use ($presets) { return $presets[$options['preset']]['alphabet']; },
            'angle'        => function(Options $options) use ($presets) { return $presets[$options['preset']]['angle']; },
            'color'        => function(Options $options) use ($presets) { return $presets[$options['preset']]['color']; },
            'format'       => function(Options $options) use ($presets) { return $presets[$options['preset']]['format']; },
            'bgcolor'      => function(Options $options) use ($presets) { return $presets[$options['preset']]['bgcolor']; },
            'shadow_color' => function(Options $options) use ($presets) { return $presets[$options['preset']]['shadow_color']; },
            'use_shadow'   => function(Options $options) use ($presets) { return $presets[$options['preset']]['use_shadow']; }
        ));
        $resolver->setAllowedValues(array(
            'preset' => array_keys($this->presets),
            'font'   => array_keys($this->fonts)
        ));
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Setup generator
        $generator = new CaptchaGenerator($options['alphabet'], $options['length'], $this->fonts[$options['font']]);
        $generator->setColors($options['color'])
                  ->setFormat($options['format'])
                  ->setMaxFontAngle($options['angle'])
                  ->setShadowColor($options['shadow_color'])
                  ->setEnableShadow($options['use_shadow']);
        if ($options['bgcolor'] === CaptchaGenerator::COLOR_TRANSPARENT) {
            $generator->setTransparentBackground()->setFormat(CaptchaGenerator::FORMAT_PNG);
        } else {
            $generator->setBackgroundColor($options['bgcolor']);
        }

        $view->vars['captcha']        = $generator->getDataSource($options['width'], $options['height']);
        $view->vars['captcha_width']  = $options['width'];
        $view->vars['captcha_height'] = $options['height'];
        $view->vars['value']          = '';

        $this->session->set($this->sessionKey, $generator->getCode());
    }
}
