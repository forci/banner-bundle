<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\Manager;

use Forci\Bundle\BannerBundle\Collection\BannerCollection;
use Forci\Bundle\BannerBundle\Entity\Banner;
use Forci\Bundle\BannerBundle\Entity\BannerPosition;
use Forci\Bundle\BannerBundle\Repository\BannerPositionRepository;
use Forci\Bundle\BannerBundle\Repository\BannerRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\RequestStack;

class BannerManager {

    /** @var BannerRepository */
    protected $bannerRepo;

    /** @var BannerPositionRepository */
    protected $bannerPositionRepo;

    /** @var RequestStack */
    protected $requestStack;

    /** @var string */
    protected $showBannerPositionsParameterName;

    /** @var CacheItemPoolInterface */
    protected $cache;

    /** @var ArrayAdapter */
    protected $localCache;

    public function __construct(BannerRepository $bannerRepo, BannerPositionRepository $bannerPositionRepo,
                                RequestStack $requestStack, string $showBannerPositionsParameterName,
                                CacheItemPoolInterface $cache) {
        $this->bannerRepo = $bannerRepo;
        $this->bannerPositionRepo = $bannerPositionRepo;
        $this->requestStack = $requestStack;
        $this->showBannerPositionsParameterName = $showBannerPositionsParameterName;
        $this->cache = $cache;
        $this->localCache = new ArrayAdapter();
    }

    public function saveBanner(Banner $banner) {
        $this->bannerRepo->save($banner);
        $this->uncachePositions();
    }

    public function savePosition(BannerPosition $position) {
        $this->bannerPositionRepo->save($position);
        $this->uncachePositions();
    }

    public function removeBanner(Banner $banner) {
        $this->bannerRepo->remove($banner);
        $this->uncachePositions();
    }

    public function removePosition(BannerPosition $position) {
        $this->bannerPositionRepo->remove($position);
        $this->uncachePositions();
    }

    public function getBanners(): BannerCollection {
        $key = 'forci_banner.banners';

        $item = $this->localCache->getItem($key);

        if ($item->isHit()) {
            /** @var BannerCollection $collection */
            $collection = $item->get();
            $collection->setDebug($this->isDebug());

            return $collection;
        }

        $positions = $this->getPositions();
        $debug = $this->isDebug();
        $collection = new BannerCollection($positions, $debug);
        $item->set($collection);
        $this->localCache->save($item);

        return $collection;
    }

    protected function isDebug(): bool {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return false;
        }

        if ($request->query->get($this->showBannerPositionsParameterName, false)) {
            return true;
        }

        return false;
    }

    public function getPositions() {
        $key = $this->getPositionsKey();

        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        $positions = $this->bannerPositionRepo->findAllActive();
        $item->set($positions);
        $this->cache->save($item);

        return $positions;
    }

    public function uncachePositions() {
        $key = $this->getPositionsKey();
        $this->cache->deleteItem($key);
    }

    protected function getPositionsKey(): string {
        return 'forci_banner.banners.positions';
    }

    public function getPositionByName($name): ?BannerPosition {
        return $this->bannerPositionRepo->findOneByName($name);
    }
}
