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
                ->arrayNode('presets')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey(true)
                    ->defaultValue(array(
                        'default' => array(
                            'width'        => 200,
                            'height'       => 70,
                            'length'       => 5,
                            'alphabet'     => 'abcdefjhkmnprstuvwxyz23456789',
                            'font'         => 'verasans',
                            'angle'        => 25,
                            'color'        => 'f00',
                            'format'       => 'png',
                            'bgcolor'      => 'fff',
                            'shadow_color' => '000',
                            'use_shadow'   => true
                        )
                    ))
                    ->prototype('array')
                        ->children()
                            ->scalarNode('width')->isRequired()->end()
                            ->scalarNode('height')->isRequired()->end()
                            ->scalarNode('length')->defaultValue(5)->end()
                            ->scalarNode('alphabet')->defaultValue('abcdefjhkmnprstuvwxyz23456789')->end()
                            ->scalarNode('font')->defaultValue('VeraSansBold')->end()
                            ->scalarNode('angle')->defaultValue(25)->end()
                            ->scalarNode('color')->defaultValue('f00')->end()
                            ->scalarNode('format')->defaultValue('png')->end()
                            ->scalarNode('bgcolor')->defaultValue('fff')->end()
                            ->scalarNode('shadow_color')->defaultValue('000')->end()
                            ->booleanNode('use_shadow')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('fonts')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey(true)
                    ->defaultValue(array(
                        'antykwa'  => array('file' => __DIR__ . '/../Resources/fonts/AntykwaBold.ttf',       'spacing' =>   -3, 'min_size' => 27, 'max_size' => 30),
                        'candice'  => array('file' => __DIR__ . '/../Resources/fonts/Candice.ttf',           'spacing' => -1.5, 'min_size' => 28, 'max_size' => 31),
                        'dingdong' => array('file' => __DIR__ . '/../Resources/fonts/Ding-DongDaddyO.ttf',   'spacing' =>   -2, 'min_size' => 24, 'max_size' => 30),
                        'duality'  => array('file' => __DIR__ . '/../Resources/fonts/Duality.ttf',           'spacing' =>   -2, 'min_size' => 30, 'max_size' => 38),
                        'heineken' => array('file' => __DIR__ . '/../Resources/fonts/Heineken.ttf',          'spacing' =>   -2, 'min_size' => 24, 'max_size' => 34),
                        'jura'     => array('file' => __DIR__ . '/../Resources/fonts/Jura.ttf',              'spacing' =>   -2, 'min_size' => 28, 'max_size' => 32),
                        'staypuft' => array('file' => __DIR__ . '/../Resources/fonts/StayPuft.ttf',          'spacing' => -1.5, 'min_size' => 28, 'max_size' => 32),
                        'times'    => array('file' => __DIR__ . '/../Resources/fonts/TimesNewRomanBold.ttf', 'spacing' =>   -2, 'min_size' => 28, 'max_size' => 34),
                        'verasans' => array('file' => __DIR__ . '/../Resources/fonts/VeraSansBold.ttf',      'spacing' =>   -1, 'min_size' => 20, 'max_size' => 28)
                    ))
                    ->prototype('array')
                        ->children()
                            ->scalarNode('file')->isRequired()->end()
                            ->scalarNode('spacing')->isRequired()->end()
                            ->scalarNode('min_size')->isRequired()->end()
                            ->scalarNode('max_size')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
