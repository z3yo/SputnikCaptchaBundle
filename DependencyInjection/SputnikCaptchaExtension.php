<?php

namespace Sputnik\Bundle\CaptchaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_DependencyInjection
 * @author Dmitri Lakachauskis <dmitri@amparo.lv>
 * @author Romuald Bulyshko <romuald@amparo.lv>
 */
class SputnikCaptchaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('sputnik_captcha.presets', $config['presets']);
        $container->setParameter('sputnik_captcha.fonts', $config['fonts']);

        $resources = $container->getParameter('twig.form.resources');
        $container->setParameter('twig.form.resources', array_merge(array('SputnikCaptchaBundle::captcha.html.twig'), $resources));
    }
}
