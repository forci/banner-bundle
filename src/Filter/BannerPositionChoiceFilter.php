<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\Filter;

use Wucdbm\Bundle\QuickUIBundle\Filter\AbstractFilter;

class BannerPositionChoiceFilter extends AbstractFilter {

    const BANNER_STATUS_HAS_BANNER = 1;
    const BANNER_STATUS_DOES_NOT_HAVE_BANNER = 2;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $bannerStatus;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getBannerStatus() {
        return $this->bannerStatus;
    }

    /**
     * @param int $bannerStatus
     */
    public function setBannerStatus($bannerStatus) {
        $this->bannerStatus = $bannerStatus;
    }
}
