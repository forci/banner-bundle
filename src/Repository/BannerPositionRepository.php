<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Forci\Bundle\BannerBundle\Entity\BannerPosition;
use Forci\Bundle\BannerBundle\Filter\BannerPositionChoiceFilter;
use Forci\Bundle\BannerBundle\Filter\BannerPositionFilter;
use Wucdbm\Bundle\QuickUIBundle\Repository\QuickUIRepositoryTrait;

class BannerPositionRepository extends EntityRepository {

    use QuickUIRepositoryTrait;

    /**
     * @param $name
     *
     * @return BannerPosition|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByName($name) {
        $builder = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->leftJoin('p.banner', 'b')
            ->andWhere('p.name = :name')
            ->setParameter('name', $name);
        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAllActive() {
        $builder = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->leftJoin('p.banner', 'b')
            ->andWhere('p.isActive = :isActive')
            ->setParameter('isActive', 1);
        $query = $builder->getQuery();

        return $query->getResult();
    }

    /**
     * @param BannerPositionFilter $filter
     *
     * @return BannerPosition[]
     */
    public function filter(BannerPositionFilter $filter) {
        $builder = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->leftJoin('p.banner', 'b');

        if ($filter->getId()) {
            $builder->andWhere('p.id = :id')
                ->setParameter('id', $filter->getId());
        }

        if ($filter->getName()) {
            $builder->andWhere('p.name = :name')
                ->setParameter('name', $filter->getName());
        }

        if (null !== $filter->getIsActive()) {
            $builder->andWhere('p.isActive = :isActive')
                ->setParameter('isActive', $filter->getIsActive());
        }

        return $this->returnFilteredEntities($builder, $filter, 'p.id');
    }

    /**
     * @param BannerPositionChoiceFilter $filter
     *
     * @return BannerPosition[]
     */
    public function filterForChoose(BannerPositionChoiceFilter $filter) {
        $builder = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->leftJoin('p.banner', 'b');

        if (null !== $filter->getBannerStatus()) {
            switch ($filter->getBannerStatus()) {
                case BannerPositionChoiceFilter::BANNER_STATUS_HAS_BANNER:
                    $builder->andWhere('p.banner IS NOT NULL');
                    break;
                case BannerPositionChoiceFilter::BANNER_STATUS_DOES_NOT_HAVE_BANNER:
                    $builder->andWhere('p.banner IS NULL');
                    break;
            }
        }

        if ($filter->getId()) {
            $builder->andWhere('p.id = :id')
                ->setParameter('id', $filter->getId());
        }

        return $this->returnFilteredEntities($builder, $filter, 'p.id');
    }

    public function save(BannerPosition $position) {
        $em = $this->getEntityManager();
        $em->persist($position);
        $em->flush();
    }

    public function remove(BannerPosition $position) {
        $em = $this->getEntityManager();
        $em->remove($position);
        $em->flush();
    }

    public function getManager() {
        return $this->getEntityManager();
    }
}
