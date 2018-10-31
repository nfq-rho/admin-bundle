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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait TranslatableCrudControllerTrait
 * @package Nfq\AdminBundle\Controller\Traits
 * @property ContainerInterface $container
 */
trait TranslatableCrudControllerTrait
{
    use CrudControllerTrait;

    /** @var string[] */
    protected $locales;

    /**
     * @Template()
     */
    public function newAction(): array
    {
        $this->loadLocales();

        $forms = [];
        foreach ($this->locales as $locale) {
            /** @var FormInterface $form  */
            [, $form] = $this->getCreateFormAndEntity($locale);
            $forms[$locale] = $form->createView();
        }

        return [
            'forms' => $forms,
        ];
    }

    /**
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $this->loadLocales();

        $forms = [];
        foreach ($this->locales as $locale) {
            /** @var FormInterface $form  */
            [$entity, $form] = $this->getCreateFormAndEntity($locale);

            if ($request->isMethod('POST') && $request->request->get($form->getName())['locale'] === $locale) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $this->insertAfterCreateAction($entity);

                    return $this->handleAfterSubmit($request, $form);
                }
            }

            $forms[$locale] = $form->createView();
        }

        return [
            'forms' => $forms,
        ];
    }

    abstract protected function getEntityForLocale($id, string $locale = null): ?object;

    /**
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function updateAction(Request $request, $id)
    {
        $this->loadLocales();

        //Correct locale for TranslatableListener is passed via event listener, so passing null here
        $baseEntity = $this->getEntityForLocale($id);

        if (!$baseEntity) {
            throw $this->createNotFoundException('Entity was not found');
        }

        $submitLocale = null;
        if ($request->isMethod('POST')) {
            if (false !== ($result = $this->doUpdate($request, $baseEntity)) instanceof RedirectResponse) {
                return $result;
            }

            $submitLocale = $result->getData()->getLocale();
        }

        $baseEntity = clone $baseEntity;

        $forms = [];
        foreach ($this->locales as $locale) {
            $editableEntity = $this->getEntityForLocale($id, $locale);
            $editableEntity->setLocale($locale);

            [$editForm, $deleteForm] = $this->getEditDeleteForms(clone $editableEntity);

            //Due to referenced base entity we have to recreate edit form for every locale, because entity of submitted
            //form changes while looping other locales thus final result of the locale entity is incorrect. But then
            //we loose form errors, so here we have to re-validate the form of submitted locale
            if ($submitLocale === $locale) {
                $editForm->handleRequest($request);
                $editForm->isValid();
            }

            $forms[$locale] = [
                'edit' => $editForm->createView(),
                'delete' => $deleteForm->createView(),
            ];
        }

        return [
            'forms' => $forms,
            'entity' => $baseEntity,
            'submitLocale' => $submitLocale,
        ];
    }

    /**
     * @return FormInterface|RedirectResponse
     */
    private function doUpdate(Request $request, $entity)
    {
        [$editForm,] = $this->getEditDeleteForms($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->saveAfterUpdateAction($entity);

            return $this->handleAfterSubmit($request, $editForm);
        }

        return $editForm;
    }

    protected function loadLocales(bool $defaultFirst = false): void
    {
        $defaultLocale = $this->container->getParameter('locale');
        $locales = $this->container->hasParameter('locales')
            ? $this->container->getParameter('locales')
            : [$defaultLocale];

        if ($defaultFirst) {
            //unset default locale and set it as first element in locales array
            $defaultIdx = array_search($defaultLocale, $locales, true);
            unset($defaultIdx);
            array_unshift($locales, $defaultLocale);
        }

        $this->locales = $locales;
    }

    protected function getEntity($id): ?object
    {
        $locale = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        return $this->getEntityForLocale($id, $locale);
    }
}
