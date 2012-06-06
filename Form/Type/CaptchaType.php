<?php

namespace Sputnik\Bundle\CaptchaBundle\Form\Type;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_Type
 * @author Dmitri Lakachauskis <dmitri@amparo.lv>
 * @author Romuald Bulyshko <romuald@amparo.lv>
 */
class CaptchaType extends AbstractType
{
    private $session;
    private $formats;
    private $sessionKey;

    /**
     * @param Session $session
     * @param array $formats
     */
    public function __construct(Session $session, array $formats)
    {
        $this->session = $session;
        $this->formats = $formats;
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
            'session_key' => null,
            'format'      => 'default',
            'width'       => null,
            'height'      => null,
            'length'      => null,
            'alphabet'    => null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOptionValues()
    {
        return array(
            'format' => array_keys($this->formats)
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
        $builder->addEventListener(FormEvents::POST_BIND, array($this, 'onPostBind'));
    }

    /**
     * @param FormEvent $event
     */
    public function onPostBind(FormEvent $event)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormViewInterface $view, FormInterface $form, array $options)
    {
        /*
        $generator = new CaptchaGenerator;
        $view->set('captcha', $generator->getInlineCode());
        $view->set('captcha_width', $width);
        $view->set('captcha_height', $height);
        */
    }
}
