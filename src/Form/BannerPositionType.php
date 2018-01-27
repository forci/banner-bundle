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
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Forci\Bundle\BannerBundle\Entity\BannerPosition;
use Forci\Bundle\BannerBundle\Manager\BannerManager;

class BannerPositionType extends AbstractType {

    /** @var BannerManager */
    protected $manager;

    public function __construct(BannerManager $manager) {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $callback = function ($object, ExecutionContextInterface $context) use ($builder) {
            $position = $this->manager->getPositionByName($object);
            /** @var BannerPosition $data */
            $data = $builder->getData();
            if ($data->getId() && $position && $data->getId() == $position->getId()) {
                return;
            }
            if ($position) {
                $context->buildViolation('A position with this name already exists')->addViolation();
            }
        };
        $builder
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Name',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Callback([
                        'callback' => $callback,
                    ]),
                ],
            ])
            ->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => 'Position description',
                'attr' => [
                    'placeholder' => 'Position description',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('banner', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                'class' => 'Forci\Bundle\BannerBundle\Entity\Banner',
                'choice_label' => 'name',
                'placeholder' => 'Banner',
                'attr' => [
                    'class' => 'select2',
                ],
                'required' => false,
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
            'data_class' => 'Forci\Bundle\BannerBundle\Entity\BannerPosition',
        ]);
    }
}
