<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Forci\Bundle\BannerBundle\Collection\BannerCollection;
use Forci\Bundle\BannerBundle\Manager\BannerManager;

class BannerExtension extends \Twig_Extension {

    /** @var BannerManager */
    protected $manager;

    /** @var \Twig_Environment */
    protected $twig;

    /** @var ContainerInterface */
    protected $container;

    /** @var BannerCollection */
    protected $collection = null;

    public function __construct(BannerManager $manager, \Twig_Environment $twig, ContainerInterface $container) {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->container = $container;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('banner', [$this, 'banner'], [
                'is_safe' => [
                    'html',
                ],
            ]),
        ];
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('banner', [$this, 'banner'], [
                'is_safe' => [
                    'html',
                ],
            ]),
            new \Twig_SimpleFunction('showBannerPositionsUrl', [$this, 'showBannerPositionsUrl']),
        ];
    }

    public function banner($name): string {
        $collection = $this->getCollection();
        if ($collection->has($name)) {
            $position = $collection->get($name);
            if ($position->getIsActive()) {
                if ($position->getBanner()) {
                    if ($position->getBanner()->getIsActive()) {
                        $data = [
                            'position' => $collection->get($name),
                            'debug' => $collection->isDebug(),
                        ];

                        return $this->twig->render('@ForciBanner/Banner/render/banner.html.twig', $data);
                    }

                    $data = [
                        'position' => $collection->get($name),
                    ];

                    return $this->twig->render('@ForciBanner/Banner/render/warning_banner_inactive.html.twig', $data);
                }

                $data = [
                    'position' => $collection->get($name),
                ];

                return $this->twig->render('@ForciBanner/Banner/render/warning_no_banner.html.twig', $data);
            }

            $data = [
                'name' => $name,
            ];

            return $this->twig->render('@ForciBanner/Banner/render/wraning_position_inactive.html.twig', $data);
        }

        $data = [
            'name' => $name,
        ];

        return $this->twig->render('@ForciBanner/Banner/render/wraning_no_position.html.twig', $data);
    }

    public function showBannerPositionsUrl(): string {
        $stack = $this->container->get('request_stack');
        $request = $stack->getCurrentRequest();
        if ($request) {
            $route = $request->attributes->get('_route');
            $routeParams = $request->attributes->get('_route_params');
            $parameter = $this->container->getParameter('forci_banner.show_positions_parameter');
            $routeParams[$parameter] = 1;
            $router = $this->container->get('router');

            return $router->generate($route, $routeParams, UrlGenerator::ABSOLUTE_URL);
        }

        return '';
    }

    protected function getCollection(): BannerCollection {
        if (null === $this->collection) {
            $this->collection = $this->manager->getBanners();
        }

        return $this->collection;
    }
}
