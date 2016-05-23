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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PlaceType
 * @package Nfq\AdminBundle\PlaceManager\Form
 */
class PlaceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('place_name', 'text', [
                'required' => false,
            ])
            ->add('places', 'choice', [
                'multiple' => true,
                'required' => false,
                'empty_value' => 'generic.placeholder.places',
                'choices' => $options['places'],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['places'])
            ->setAllowedTypes(['places' => 'array']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'place';
    }
}
