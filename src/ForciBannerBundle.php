<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Forci\Bundle\BannerBundle\DependencyInjection\Compiler\EntityManagerCompiler;

class ForciBannerBundle extends Bundle {

    public function build(ContainerBuilder $container) {
        parent::build($container);

        $container->addCompilerPass(new EntityManagerCompiler());
    }
}
