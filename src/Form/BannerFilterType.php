<?php

/*
 * This file is part of the ForciBannerBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\BannerBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Forci\Bundle\BannerBundle\Filter\BannerFilter;
use Wucdbm\Bundle\QuickUIBundle\Form\Filter\BaseFilterType;

class BannerFilterType extends BaseFilterType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('isActive', 'Wucdbm\Bundle\QuickUIBundle\Form\Filter\ChoiceFilterType', [
                'placeholder' => 'Status',
                'choices' => [
                    'Active' => BannerFilter::IS_ACTIVE_TRUE,
                    'Inactive' => BannerFilter::IS_ACTIVE_FALSE,
                ],
            ])
            ->add('id', 'Wucdbm\Bundle\QuickUIBundle\Form\Filter\TextFilterType', [
                'placeholder' => 'ID',
            ])
            ->add('name', 'Wucdbm\Bundle\QuickUIBundle\Form\Filter\TextFilterType', [
                'placeholder' => 'Name',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Forci\Bundle\BannerBundle\Filter\BannerFilter',
        ]);
    }
}
