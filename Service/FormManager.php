<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FormManager
 * @package Nfq\AdminBundle\Service
 */
class FormManager
{
    const SUBMIT_DISABLED = 0;
    const SUBMIT_STANDARD = 1;
    const SUBMIT_CLOSE = 2;
    const SUBMIT_PREVIEW = 4;
    const SUBMIT_DELETE = 8;

    const CRUD_CREATE = 'create';
    const CRUD_UPDATE = 'update';
    const CRUD_DELETE = 'delete';

    /**
     * @var FormFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $methods = [
        self::CRUD_CREATE => "POST",
        self::CRUD_UPDATE => "POST",
        self::CRUD_DELETE => "POST",
    ];

    /**
     * @param FormFactory $factory
     */
    public function __construct(FormFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->factory;
    }

    /**
     * Builds delete form for controllers
     *
     * @param string $uri
     * @param int $submit
     * @return Form
     */
    public function getDeleteForm($uri, $submit = self::SUBMIT_DELETE)
    {
        $formBuilder = $this->getFormBuilder($uri, self::CRUD_DELETE, null, null, [], $submit);

        return $formBuilder->getForm();
    }

    /**
     * Builds delete form for controllers
     *
     * @deprecated - use getDeleteForm
     * @param string $uri
     * @param bool $submit
     * @return Form
     */
    public function deleteForm($uri, $submit = false)
    {
        if ($submit !== false) {
            $submit = self::SUBMIT_DELETE;
        }

        return $this->getDeleteForm($uri, (int)$submit);
    }

    /**
     * @return string
     */
    public function getDeleteMethod()
    {
        return $this->methods[self::CRUD_DELETE];
    }

    /**
     * @param string $deleteMethod
     *
     * @return $this
     */
    public function setDeleteMethod($deleteMethod)
    {
        $this->methods[self::CRUD_DELETE] = $deleteMethod;

        return $this;
    }

    /**
     * Builds edit form for controllers
     *
     * @param string $uri
     * @param object $formType
     * @param mixed $data
     * @param array $formOptions
     * @param int $submit
     * @return Form
     */
    public function getEditForm($uri, $formType, $data, $formOptions = [], $submit = self::SUBMIT_STANDARD)
    {
        $formBuilder = $this->getFormBuilder($uri, self::CRUD_UPDATE, $formType, $data, $formOptions, $submit);

        return $formBuilder->getForm();
    }

    /**
     * Builds edit form for controllers
     *
     * @deprecated - use getEditForm
     * @param string $uri
     * @param object $formType
     * @param mixed $data
     * @param array $formOptions
     * @param bool $submit
     * @return Form
     */
    public function editForm($uri, $formType, $data, $formOptions = [], $submit = false)
    {
        return $this->getEditForm($uri, $formType, $data, $formOptions, (int)$submit);
    }

    /**
     * @return string
     */
    public function getEditMethod()
    {
        return $this->methods[self::CRUD_UPDATE];
    }

    /**
     * @param string $editMethod
     *
     * @return $this
     */
    public function setEditMethod($editMethod)
    {
        $this->methods[self::CRUD_UPDATE] = $editMethod;

        return $this;
    }

    /**
     * Create createForm for controllers
     *
     * @param string $uri
     * @param object $formType
     * @param mixed $data
     * @param array $formOptions
     * @param int $submit
     * @return Form
     */
    public function getCreateForm(
        $uri,
        $formType,
        $data = null,
        array $formOptions = [],
        $submit = self::SUBMIT_STANDARD
    ) {
        $formBuilder = $this->getFormBuilder($uri, self::CRUD_CREATE, $formType, $data, $formOptions, $submit);

        return $formBuilder->getForm();
    }

    /**
     * Create createForm for controllers
     *
     * @deprecated - use getCreateForm
     * @param string $uri
     * @param object $formType
     * @param mixed $data
     * @param array $formOptions
     * @param bool $submit
     * @return Form
     */
    public function createForm($uri, $formType, $data = null, $formOptions = [], $submit = false)
    {
        return $this->getCreateForm($uri, $formType, $data, $formOptions, (int)$submit);
    }

    /**
     * @return string
     */
    public function getCreateMethod()
    {
        return $this->methods[self::CRUD_CREATE];
    }

    /**
     * @param string $createMethod
     *
     * @return $this
     */
    public function setCreateMethod($createMethod)
    {
        $this->methods[self::CRUD_CREATE] = $createMethod;

        return $this;
    }

    /**
     * @param string $action
     * @param string $method
     * @param $formType
     * @param null $data
     * @param array $formOptions
     * @param int $submit
     * @return FormBuilderInterface
     */
    public function getFormBuilder(
        $action,
        $method,
        $formType,
        $data = null,
        array $formOptions = [],
        $submit = self::SUBMIT_STANDARD
    ) {
        $submitOptions = $this->getSubmitOptions($submit);

        $formType = is_null($formType) ? FormType::class : $formType;

        $formBuilder = $this->getFormFactory()->createBuilder($formType, $data, $formOptions);
        $formBuilder->setAction($action)->setMethod($this->methods[$method]);

        isset($submitOptions[self::SUBMIT_STANDARD]) && $formBuilder->add('submit', SubmitType::class,
            $submitOptions[self::SUBMIT_STANDARD]);
        isset($submitOptions[self::SUBMIT_CLOSE]) && $formBuilder->add('submit_close', SubmitType::class,
            $submitOptions[self::SUBMIT_CLOSE]);
        isset($submitOptions[self::SUBMIT_PREVIEW]) && $formBuilder->add('submit_preview', SubmitType::class,
            $submitOptions[self::SUBMIT_PREVIEW]);
        isset($submitOptions[self::SUBMIT_DELETE]) && $formBuilder->add('submit_delete', SubmitType::class,
            $submitOptions[self::SUBMIT_DELETE]);

        return $formBuilder;
    }

    /**
     * Sets submit options from given submit mode
     *
     * @param int $submit
     * @return array
     */
    private function getSubmitOptions($submit)
    {
        $submitOptions = [];

        if (self::SUBMIT_PREVIEW === ($submit & self::SUBMIT_PREVIEW)) {
            $submitOptions[self::SUBMIT_PREVIEW] = [
                'attr' => ['class' => 'btn-success btn-submit btn-submit-preview'],
                'label' => "ADMIN.DEFAULT_SUBMIT_PREVIEW"
            ];
        }

        if (self::SUBMIT_CLOSE === ($submit & self::SUBMIT_CLOSE)) {
            $submitOptions[self::SUBMIT_CLOSE] = [
                'attr' => ['class' => 'btn-success btn-submit btn-submit-close'],
                'label' => "ADMIN.DEFAULT_SUBMIT_CLOSE"
            ];
        }

        if (self::SUBMIT_STANDARD === ($submit & self::SUBMIT_STANDARD)) {
            $submitOptions[self::SUBMIT_STANDARD] = [
                'attr' => ['class' => 'btn-success btn-submit'],
                'label' => "ADMIN.DEFAULT_SUBMIT"
            ];
        }

        if (self::SUBMIT_DELETE === ($submit & self::SUBMIT_DELETE)) {
            $submitOptions[self::SUBMIT_DELETE] = [
                'attr' => ['class' => 'btn-danger btn-delete'],
                'label' => "ADMIN.DEFAULT_DELETE"
            ];
        }

        return $submitOptions;
    }
}
