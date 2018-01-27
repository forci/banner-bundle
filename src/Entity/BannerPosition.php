<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Forci\Bundle\BannerBundle\Repository\BannerPositionRepository")
 * @ORM\Table(name="_forci__banners_positions",
 *      options={"collate"="utf8_general_ci"},
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="name", columns={"name"})
 *      }
 * )
 */
class BannerPosition {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive = true;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\BannerBundle\Entity\Banner", inversedBy="positions")
     * @ORM\JoinColumn(name="banner_id", referencedColumnName="id")
     */
    protected $banner;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return BannerPosition
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return BannerPosition
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return BannerPosition
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Set banner.
     *
     * @param \Forci\Bundle\BannerBundle\Entity\Banner $banner
     *
     * @return BannerPosition
     */
    public function setBanner(\Forci\Bundle\BannerBundle\Entity\Banner $banner = null) {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner.
     *
     * @return \Forci\Bundle\BannerBundle\Entity\Banner
     */
    public function getBanner() {
        return $this->banner;
    }
}
