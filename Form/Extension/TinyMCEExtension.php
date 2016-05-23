<?php

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
    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['attr']['class']) && strpos($options['attr']['class'], 'tinymce') !== false) {
            $this->setElementId($view);
        }
    }

    /**
     * @param FormView $view
     */
    private function setElementId(FormView $view)
    {
        $view->vars['id'] .= '_' . substr(md5(mt_rand(0, 250) . time() . mt_rand(251, 500)), 0, 10);
    }

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return 'textarea';
    }
}
