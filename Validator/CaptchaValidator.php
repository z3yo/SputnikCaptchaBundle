<?php

namespace Sputnik\Bundle\CaptchaBundle\Validator;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormValidatorInterface;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_Validator
 * @author Romuald Bulyshko <romuald@amparo.lv>
 * @author Dmitri Lakachauskis <dmitri@amparo.lv>
 */
class CaptchaValidator implements FormValidatorInterface
{
    private $session;
    private $key;

    /**
     * @param SessionInterface $session
     * @param string $key
     */
    public function __construct(SessionInterface $session, $key)
    {
        $this->session = $session;
        $this->key     = $key;
    }

    /**
     * @param FormInterface $form
     */
    public function validate(FormInterface $form)
    {
        $code     = $form->getData();
        $excepted = $this->getExceptedCode();

        if (strtolower($code) !== strtolower($excepted)) {
            $form->addError(new FormError('invalid captcha value'));
        }

        $this->session->remove($this->key);
    }

    /**
     * @return string|null
     */
    private function getExceptedCode()
    {
        $result = null;
        if ($this->session->has($this->key)) {
            $result = $this->session->get($this->key);
        }
        return $result;
    }
}
