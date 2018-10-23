<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CrudNewCreateController
 * @package Nfq\AdminBundle\Controller\Traits
 */
trait CrudNewCreateController
{

    /**
     * Displays a form to create a new  entity.
     *
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(): array
    {
        [$entity, $form] = $this->getCreateFormAndEntity();

        return [
            'entity' => $entity,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new entity.
     *
     * @Route("/new")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        [$entity, $form] = $this->getCreateFormAndEntity();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->insertAfterCreateAction($entity);
            return $this->redirectToIndex($request, $entity);
        }

        return [
            'entity' => $entity,
            'form' => $form->createView(),
        ];
    }

    /**
     * Generate url and redirect to it. Request and entity is passed for convenience
     */
    abstract protected function redirectToIndex(Request $request, $entity = null): RedirectResponse;

    /**
     * Creates form and entity
     * @return array<$entity, FormInterface $createForm>
     */
    abstract protected function getCreateFormAndEntity(): array;

    /**
     * Save entity after insert
     * @param $entity
     */
    abstract protected function insertAfterCreateAction($entity): void;
}
