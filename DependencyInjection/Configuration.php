<?php

namespace Sputnik\Bundle\CaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @category SputnikCaptchaBundle
 * @package SputnikCaptchaBundle_DependencyInjection
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
        $rootNode    = $treeBuilder->root('sputnik_captcha', 'array');

        /**
         * Example:
         *
         * sputnik_captcha:
         *     width:  200
         *     height: 70
         */
        $rootNode
            ->children()
                ->arrayNode()
                    ->prototype()
                        ->scalarNode('width', 200)
                        ->scalarNode('height', 70)
                        ->scalarNode('length', 5)
                        ->scalarNode('alphabet', 'abz')
                    ->end()
                ->end()

                ->scalar
                ->scalarNode('width')->defaultValue(200)->end()
                ->scalarNode('height')->defaultValue(70)->end()

//                ->scalarNode('keep_value')->defaultValue(true)->end()
//                 ->scalarNode('as_file')->defaultValue(false)->end()
//                 ->scalarNode('image_folder')->defaultValue('captcha')->end()
//                 ->scalarNode('web_path')->defaultValue('%kernel.root_dir%/../web')->end()
//                 ->scalarNode('expiration')->defaultValue(60)->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
