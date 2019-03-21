<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * This class is used to set unique ID for tinyMCE textareas
 *
 * Class TinyMCEExtension
 * @package Nfq\AdminBundle\Form\Extension
 */
class TinyMCEExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['attr']['class']) && strpos($options['attr']['class'], 'tinymce') !== false) {
            $this->setElementId($view);
        }
    }

    private function setElementId(FormView $view): void
    {
        $view->vars['id'] .= '_' . substr(md5(mt_rand(0, 250) . time() . mt_rand(251, 500)), 0, 10);
    }

    // Kept for BC
    public function getExtendedType(): string
    {
        return TextareaType::class;
    }

    public static function getExtendedTypes(): array
    {
        return [TextareaType::class];
    }
}
