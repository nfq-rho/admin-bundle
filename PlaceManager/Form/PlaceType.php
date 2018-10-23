<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\PlaceManager\Form;

use Nfq\AdminBundle\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PlaceType
 * @package Nfq\AdminBundle\PlaceManager\Form
 */
class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('placeTitleOverwrite', TextType::class, [
                'required' => false,
            ])
            ->add('places', ChoiceType::class, [
                'multiple' => true,
                'required' => false,
                'placeholder' => 'admin.generic.placeholder.places',
                'choices' => array_flip($options['places']),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['places'])
            ->setAllowedTypes('places', 'array');
    }
}
