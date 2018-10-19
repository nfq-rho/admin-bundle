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
trait AbstractCrudController
{
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

    /**
     * Deletes an entity.
     *
     * @Route("/{id}/delete")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->getDeleteForm($id);
        $entity = $this->getEntity($id);

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

    protected function getRedirectToIndexParams(Request $request, $entity): ParameterBag
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

        if (is_object($entity) && method_exists($entity, 'getId')) {
            $redirectParams->set('reopen_id', $entity->getId());
        } else {
            $redirectParams->remove('reopen_id');
        }

        return $redirectParams;
    }

    /**
     * @throws \RuntimeException
     */
    protected function redirectToIndex(Request $request, $entity = null): RedirectResponse
    {
        throw new \RuntimeException('Implement this method');
    }

    /**
     * @throws \RuntimeException
     */
    protected function redirectToPreview($entity): RedirectResponse
    {
        throw new \RuntimeException('Implement this method');
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

    protected function getFormService(): FormManager
    {
        return $this->get(FormManager::class);
    }

    /**
     * Create and return edit and delete forms. If edit form was created before when submitting data,
     * $editForm will contain that form with all validation errors
     *
     * @return FormInterface[]
     */
    abstract protected function getEditDeleteForms($entity): array;

    abstract protected function getDeleteForm($id): FormInterface;

    abstract protected function deleteAfterDeleteAction($entity): void;

    abstract protected function saveAfterUpdateAction($entity): void;

    /**
     * @throws NotFoundHttpException
     */
    abstract protected function getEntity($id);
}
