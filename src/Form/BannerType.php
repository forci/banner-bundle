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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BannerType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Name',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('content', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
                'label' => 'Content',
                'attr' => [
                    'rows' => 16,
                    'placeholder' => 'Put your banner HTML here',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('isActive', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => 'Active',
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Forci\Bundle\BannerBundle\Entity\Banner',
        ]);
    }
}
