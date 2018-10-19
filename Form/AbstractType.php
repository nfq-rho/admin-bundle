<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Form;

use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractType
 * @package Nfq\AdminBundle\Form
 */
abstract class AbstractType extends BaseAbstractType
{
    abstract protected function callBuildForm(FormBuilderInterface $builder, array $options): void;

    abstract protected function callConfigureOptions(OptionsResolver $resolver): void;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->callBuildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'adminInterface',
        ]);

        $this->callConfigureOptions($resolver);
    }
}
