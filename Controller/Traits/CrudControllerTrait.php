<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Controller\Traits;

use Nfq\AdminBundle\Service\FormManager;
use Symfony\Component\Form\FormInterface;
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
trait CrudControllerTrait
{
    /** @var FormManager */
    private $formManager;

    /**
     * @required
     */
    public function setFormManager(FormManager $formManager): void
    {
        $this->formManager = $formManager;
    }

    protected function getFormManager(): FormManager
    {
        return $this->formManager;
    }

    abstract protected function getEntity($id): ?object;

    /**
     * @return array<$entity, FormInterface $createForm>
     */
    abstract protected function getCreateFormAndEntity(): array;

    abstract protected function insertAfterCreateAction(object $entity): void;

    /**
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
     * @return array<$entity, FormInterface $createForm>
     */
    abstract protected function getEditDeleteForms(object $entity): array;

    abstract protected function saveAfterUpdateAction(object $entity): void;

    /**
     * Edits an existing entity.
     *
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        if (!$entity) {
            throw $this->createNotFoundException('Entity not found.');
        }

        [$editForm, $deleteForm] = $this->getEditDeleteForms($entity);

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

    abstract protected function deleteAfterDeleteAction(object $entity): void;

    /**
     * @Route("/{id}/delete")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        if (!$entity) {
            throw $this->createNotFoundException('Entity not found.');
        }

        $form = $this->getDeleteForm($id);

        if ($request->isMethod('POST')) {
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

    protected function handleAfterSubmit(Request $request, FormInterface $submittedForm): RedirectResponse
    {
        $entity = $submittedForm->getData();

        if ($submittedForm->has('submit_preview') && $submittedForm->get('submit_preview')->isClicked()) {
            return $this->redirectToPreview($entity);
        }

        if ($submittedForm->has('submit_close') && $submittedForm->get('submit_close')->isClicked()) {
            return $this->redirectToIndex($request);
        }

        return $this->redirectToIndex($request, $entity);
    }

    /**
     * @throws \RuntimeException
     */
    protected function redirectToPreview(object $entity): RedirectResponse
    {
        throw new \RuntimeException('Implement this method');
    }

    /**
     * @throws \RuntimeException
     */
    protected function redirectToIndex(Request $request, ?object $entity = null): RedirectResponse
    {
        throw new \RuntimeException('Implement this method');
    }

    protected function getRedirectToIndexParams(Request $request, ?object $entity): ParameterBag
    {
        $redirectParams = new ParameterBag();

        if ($httpReferrer = $request->server->get('HTTP_REFERER')) {
            $query = parse_url($httpReferrer, PHP_URL_QUERY);

            if (!empty($query)) {
                parse_str($query, $referrerParams);
                $referrerParams = array_filter($referrerParams);
                $redirectParams->add($referrerParams);
            }
        }

        if (\is_object($entity)) {
            method_exists($entity, 'getId') && $redirectParams->set('reopen_id', $entity->getId());
            method_exists($entity, 'getLocale') && $redirectParams->set('reopen_locale', $entity->getLocale());
        } else {
            $redirectParams->remove('reopen_id');
            $redirectParams->remove('reopen_locale');
        }

        return $redirectParams;
    }
}
