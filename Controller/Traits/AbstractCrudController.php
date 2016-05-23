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

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AbstractCrudController
 * @package Nfq\AdminBundle\Controller\Traits
 */
trait AbstractCrudController
{
    /**
     * Displays a form to edit an existing entity.
     * @deprecated
     * @Route("/{id}/edit")
     * @Method("GET")
     * @Template()
     *
     * @param int $id
     * @return array
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);

        /** @var Form $editForm
         *  @var Form $deleteForm */
        list($editForm, $deleteForm) = $this->getEditDeleteForms($entity);

        return [
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Edits an existing entity.
     *
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        /**
         * @var Form $editForm
         * @var Form $deleteForm
         */
        list($editForm, $deleteForm) = $this->getEditDeleteForms($entity);

        if ($request->isMethod('POST')) {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $this->saveAfterUpdateAction($entity);

                return $this->handleAfterSubmit($request, $editForm);
            }
        }

        return [
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * @deprecated
     * @Route("/{id}/delete")
     * @Method("GET")
     * @Template()
     *
     * @param int $id
     * @return array
     */
    public function removeAction($id)
    {
        /* @var Form $form */
        $form = $this->getDeleteForm($id);
        $entity = $this->getEntity($id);

        return [
            'entity' => $entity,
            'delete_form' => $form->createView(),
        ];
    }

    /**
     * Deletes an entity.
     *
     * @Route("/{id}/delete")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->getDeleteForm($id);
        $entity = $this->getEntity($id);

        if ($request->isMethod('POST')) {
            /* @var Form $form */
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->deleteAfterDeleteAction($entity);
                return $this->redirectToIndex($request);
            }
        }

        return [
            'entity' => $entity,
            'delete_form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param mixed $entity
     * @return ParameterBag
     */
    protected function getRedirectToIndexParams(Request $request, $entity)
    {
        $redirectParams = new ParameterBag();

        if ($httpReferrer = $request->server->get('HTTP_REFERER')) {
            $query = parse_url($httpReferrer, PHP_URL_QUERY);
            parse_str($query, $referrerParams);
            $referrerParams = array_filter($referrerParams);

            $redirectParams->add($referrerParams);
        }

        if (is_object($entity) && method_exists($entity, 'getId')) {
            $redirectParams->set('reopen_id', $entity->getId());
        } else {
            $redirectParams->remove('reopen_id');
        }

        return $redirectParams;
    }

    /**
     * @param Request $request
     * @param null $entity
     * @throws \RuntimeException
     * @return RedirectResponse
     */
    protected function redirectToIndex(Request $request, $entity = null)
    {
        throw new \RuntimeException('Implement this method');
    }

    /**
     * @param $entity
     * @throws \RuntimeException
     * @return RedirectResponse
     */
    protected function redirectToPreview($entity)
    {
        throw new \RuntimeException('Implement this method');
    }

    /**
     * @param Request $request
     * @param Form $submittedForm
     * @return RedirectResponse
     */
    protected function handleAfterSubmit(Request $request, Form $submittedForm)
    {
        $entity = $submittedForm->getData();

        if ($submittedForm->has('submit_preview') && $submittedForm->get('submit_preview')->isClicked()) {
            return $this->redirectToPreview($entity);
        }

        if ($submittedForm->has('submit_close') && $submittedForm->get('submit_close')->isClicked()) {
            return $this->redirectToIndex($request, null);
        }

        return $this->redirectToIndex($request, $entity);
    }

    /**
     * Get form service
     * @return \Nfq\AdminBundle\Service\FormManager
     */
    protected function getFormService()
    {
        return $this->get('nfq_admin.form_service');
    }

    /**
     * Create and return edit and delete forms. If edit form was created before when submitting data,
     * $editForm will contain that form with all validation errors
     *
     * @param mixed $entity
     * @return mixed
     */
    abstract protected function getEditDeleteForms($entity);

    /**
     * @param $id
     * @return Form $form
     */
    abstract protected function getDeleteForm($id);

    /**
     * Delete entity
     * @param $entity
     */
    abstract protected function deleteAfterDeleteAction($entity);
    /**
     * Save entity after update
     * @param $entity
     */
    abstract protected function saveAfterUpdateAction($entity);

    /**
     * Returns Entity
     * @param $id
     * @return mixed $entity
     * @throws NotFoundHttpException
     */
    abstract protected function getEntity($id);
}
