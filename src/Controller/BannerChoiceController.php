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
use Forci\Bundle\BannerBundle\Filter\BannerPositionChoiceFilter;
use Forci\Bundle\BannerBundle\Form\BannerPositionChoiceFilterType;
use Forci\Bundle\BannerBundle\Form\BannerPositionChooseType;
use Forci\Bundle\BannerBundle\Manager\BannerManager;
use Forci\Bundle\BannerBundle\Repository\BannerPositionRepository;

class BannerChoiceController extends Controller {

    /** @var BannerManager */
    protected $bannerManager;

    /** @var BannerPositionRepository */
    protected $bannerPositionRepo;

    public function __construct(BannerManager $bannerManager, BannerPositionRepository $bannerPositionRepo) {
        $this->bannerManager = $bannerManager;
        $this->bannerPositionRepo = $bannerPositionRepo;
    }

    public function chooseAction(Request $request) {
        $filter = new BannerPositionChoiceFilter();
        $pagination = $filter->getPagination()->enable();
        $filter->loadFromRequest($request);
        $filterForm = $this->createForm(BannerPositionChoiceFilterType::class, $filter);
        $filter->load($request, $filterForm);

        $positions = $this->bannerPositionRepo->filterForChoose($filter);

        $forms = [];
        /** @var BannerPosition $position */
        foreach ($positions as $position) {
            $forms[$position->getId()] = $this->createForm(BannerPositionChooseType::class, $position, [
                'attr' => [
                    'class' => 'position-form',
                ],
                'action' => $this->generateUrl('forci_banner_choice_update_banner', [
                    'id' => $position->getId(),
                ]),
            ])->createView();
        }

        $data = [
            'filter' => $filter,
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(),
            'forms' => $forms,
        ];

        return $this->render('@ForciBanner/BannerChoice/choose.html.twig', $data);
    }

    public function updatePositionBannerAction(BannerPosition $position, Request $request) {
        $form = $this->createForm(BannerPositionChooseType::class, $position);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->bannerManager->savePosition($position);

            $banner = $position->getBanner();

            if ($banner) {
                $bannerAddon = sprintf('will display Banner <b>%s</b>', $banner->getName());
            } else {
                $bannerAddon = 'will <b>no longer</b> show a banner.';
            }

            $msg = sprintf('Banner Position <b>%s</b> %s', $position->getName(), $bannerAddon);

            return $this->json([
                'message' => [
                    'text' => $msg,
                ],
            ]);
        }

        return $this->json([
            'message' => [
                'title' => 'Error',
                'text' => 'There was an error trying to save the selected banner',
            ],
        ]);
    }
}
