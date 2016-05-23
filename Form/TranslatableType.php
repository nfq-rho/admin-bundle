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
use Symfony\Component\Form\FormBuilderInterface;
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
        $builder->add('locale', 'hidden');

        $this->callBuildForm($builder, $options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    abstract protected function callBuildForm(FormBuilderInterface $builder, array $options);

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['locale']);

        $this->callSetDefaultOptions($resolver);
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return void
     */
    abstract protected function callSetDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * @return string
     */
    public function getName()
    {
        return 'translatable';
    }
}
