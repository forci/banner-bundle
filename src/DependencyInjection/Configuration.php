<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('forci_banner');

        $rootNode
            ->children()
//                ->scalarNode('show_positions_parameter')
//                    ->defaultValue('showpositions')
//                ->end()
                ->scalarNode('entity_manager_name')
                    ->defaultValue('default')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
