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

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TranslatableType
 * @package Nfq\AdminBundle\Form
 */
abstract class TranslatableType extends AbstractType
{
    /** @var string */
    protected $locale;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->locale = $options['locale'];
        $builder->add('locale', HiddenType::class);

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['locale']);

        parent::configureOptions($resolver);
    }
}
