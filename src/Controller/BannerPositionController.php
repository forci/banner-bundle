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

namespace Forci\Bundle\BannerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Forci\Bundle\BannerBundle\Entity\BannerPosition;
use Forci\Bundle\BannerBundle\Filter\BannerPositionFilter;
use Forci\Bundle\BannerBundle\Form\BannerPositionFilterType;
use Forci\Bundle\BannerBundle\Form\BannerPositionType;
use Forci\Bundle\BannerBundle\Manager\BannerManager;
use Forci\Bundle\BannerBundle\Repository\BannerPositionRepository;

class BannerPositionController extends Controller {

    /** @var BannerManager */
    protected $bannerManager;

    /** @var BannerPositionRepository */
    protected $bannerPositionRepo;

    public function __construct(BannerManager $bannerManager, BannerPositionRepository $bannerPositionRepo) {
        $this->bannerManager = $bannerManager;
        $this->bannerPositionRepo = $bannerPositionRepo;
    }

    public function listAction(Request $request) {
        $filter = new BannerPositionFilter();
        $pagination = $filter->getPagination()->enable();
        $filterForm = $this->createForm(BannerPositionFilterType::class, $filter);
        $filter->load($request, $filterForm);
        $positions = $this->bannerPositionRepo->filter($filter);
        $data = [
            'positions' => $positions,
            'filter' => $filter,
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(),
        ];

        return $this->render('@ForciBanner/BannerPosition/list.html.twig', $data);
    }

    public function refreshAction(BannerPosition $position) {
        $data = [
            'position' => $position,
        ];

        return $this->render('@ForciBanner/BannerPosition/list_row.html.twig', $data);
    }

    public function activateAction(BannerPosition $position) {
        return $this->activity($position, true);
    }

    public function deactivateAction(BannerPosition $position) {
        return $this->activity($position, false);
    }

    protected function activity(BannerPosition $position, $boolean) {
        $position->setIsActive($boolean);

        $this->bannerManager->savePosition($position);

        return $this->json([
            'success' => true,
            'refresh' => true,
        ]);
    }

    public function editAction(BannerPosition $position, Request $request) {
        $form = $this->createForm(BannerPositionType::class, $position);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->bannerManager->savePosition($position);
        }

        $data = [
            'position' => $position,
            'form' => $form->createView(),
        ];

        return $this->render('@ForciBanner/BannerPosition/edit.html.twig', $data);
    }

    public function createAction(Request $request) {
        $position = new BannerPosition();
        $name = $request->query->get('name');
        if ($name) {
            $position->setName($name);
        }
        $form = $this->createForm(BannerPositionType::class, $position);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->bannerManager->savePosition($position);

            return $this->redirectToRoute('forci_banner_position_edit', [
                'id' => $position->getId(),
            ]);
        }

        $data = [
            'form' => $form->createView(),
        ];

        return $this->render('@ForciBanner/BannerPosition/create.html.twig', $data);
    }

    public function deleteAction(BannerPosition $position, Request $request) {
        if (!$request->request->get('is_confirmed')) {
            return $this->json([
                'success' => false,
            ]);
        }

        $this->bannerManager->removePosition($position);

        return $this->json([
            'success' => true,
            'remove' => true,
            'witter' => [
                'text' => 'Position deleted',
            ],
        ]);
    }
}
