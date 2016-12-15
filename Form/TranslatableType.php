<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TranslatableType
 * @package Nfq\AdminBundle\Form
 */
abstract class TranslatableType extends AbstractType
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->locale = $options['locale'];
        $builder->add('locale', HiddenType::class);

        $this->callBuildForm($builder, $options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    abstract protected function callBuildForm(FormBuilderInterface $builder, array $options);

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $this->setDefaultOptions($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['locale']);

        $this->callSetDefaultOptions($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    abstract protected function callSetDefaultOptions(OptionsResolver $resolver);

    /**
     * @return string
     */
    public function getName()
    {
        return 'translatable';
    }
}
