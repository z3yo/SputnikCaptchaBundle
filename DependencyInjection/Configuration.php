<?php

namespace Sputnik\Bundle\CaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_DependencyInjection
 * @author Dmitri Lakachauskis <dmitri@amparo.lv>
 * @author Romuald Bulyshko <romuald@amparo.lv>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('sputnik_captcha');
        $rootNode
            ->children()
                ->arrayNode('formats')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey(true)
                    ->defaultValue(array(
                        'default' => array(
                            'width'    => 200,
                            'height'   => 70,
                            'length'   => 5,
                            'alphabet' => 'abcdefjhkmnprstuvwxyz23456789',
                            'font'     => 'VeraSansBold'
                        )
                    ))
                    ->prototype('array')
                        ->children()
                            ->scalarNode('width')->isRequired()->end()
                            ->scalarNode('height')->isRequired()->end()
                            ->scalarNode('length')->defaultValue(5)->end()
                            ->scalarNode('alphabet')->defaultValue('abcdefjhkmnprstuvwxyz23456789')->end()
                            ->scalarNode('font')->defaultValue('VeraSansBold')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
