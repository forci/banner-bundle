<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\Collection;

use Forci\Bundle\BannerBundle\Entity\BannerPosition;

class BannerCollection {

    protected $positions = [];
    protected $debug = false;

    /**
     * @param BannerPosition[] $positions
     * @param bool             $debug
     */
    public function __construct(array $positions, $debug = false) {
        $this->debug = $debug;
        foreach ($positions as $pos) {
            $this->add($pos);
        }
    }

    /**
     * @param BannerPosition $position
     */
    public function add(BannerPosition $position) {
        $this->positions[$position->getName()] = $position;
    }

    /**
     * @param array $positions
     */
    public function addArray(array $positions) {
        foreach ($positions as $position) {
            $this->add($position);
        }
    }

    /**
     * @param $name
     *
     * @return BannerPosition|null
     */
    public function get($name) {
        return isset($this->positions[$name]) ? $this->positions[$name] : null;
    }

    /**
     * Has position.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name) {
        return isset($this->positions[$name]);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasBanner($name) {
        /** @var BannerPosition $position */
        $position = $this->get($name);
        if (null === $position) {
            return false;
        }
        $banner = $position->getBanner();
        if (null === $banner) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isDebug() {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }
}
