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

namespace Forci\Bundle\BannerBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Forci\Bundle\BannerBundle\Filter\BannerPositionFilter;
use Wucdbm\Bundle\QuickUIBundle\Form\Filter\BaseFilterType;
use Wucdbm\Bundle\QuickUIBundle\Form\Filter\ChoiceFilterType;
use Wucdbm\Bundle\QuickUIBundle\Form\Filter\TextFilterType;

class BannerPositionFilterType extends BaseFilterType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('isActive', ChoiceFilterType::class, [
                'placeholder' => 'Status',
                'choices' => [
                    'Active' => BannerPositionFilter::IS_ACTIVE_TRUE,
                    'Inactive' => BannerPositionFilter::IS_ACTIVE_FALSE,
                ],
            ])
            ->add('name', TextFilterType::class, [
                'placeholder' => 'Name',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => BannerPositionFilter::class,
        ]);
    }
}
