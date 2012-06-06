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
    private $formats;
    private $sessionKey;

    /**
     * @param SessionInterface $session
     * @param array $formats
     */
    public function __construct(SessionInterface $session, array $formats)
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
        // override default format options
        $params = $this->formats[$options['format']];
        foreach (array('width', 'height', 'length', 'alphabet') as $option) {
            if (isset($options[$option])) {
                $params[$option] = $options[$option];
            }
        }
        extract($params);

        $generator = new CaptchaGenerator($alphabet, $length);
        $view->setVar('captcha', $code = $generator->getCode());
        $view->setVar('value', '');
        $this->session->set($this->sessionKey, $code);
    }
}
