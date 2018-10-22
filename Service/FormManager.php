<?php declare(strict_types=1);

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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class FormManager
 * @package Nfq\AdminBundle\Service
 */
class FormManager
{
    public const SUBMIT_DISABLED = 0;
    public const SUBMIT_STANDARD = 1;
    public const SUBMIT_CLOSE = 2;
    public const SUBMIT_PREVIEW = 4;
    public const SUBMIT_DELETE = 8;

    public const CRUD_CREATE = 'create';
    public const CRUD_UPDATE = 'update';
    public const CRUD_DELETE = 'delete';

    /** @var FormFactoryInterface */
    protected $factory;

    /** @var array */
    protected $methods = [
        self::CRUD_CREATE => 'POST',
        self::CRUD_UPDATE => 'POST',
        self::CRUD_DELETE => 'POST',
    ];

    /** @var string[]  */
    private $defaultFormOptions = [
        'translation_domain' => 'adminInterface',
    ];

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->factory;
    }

    /**
     * Builds delete form for controllers.
     */
    public function getDeleteForm(string $uri, int $submit = self::SUBMIT_DELETE): FormInterface
    {
        $formBuilder = $this->getFormBuilder($uri, self::CRUD_DELETE, null, null, [], $submit);

        return $formBuilder->getForm();
    }

    public function getDeleteMethod(): string
    {
        return $this->methods[self::CRUD_DELETE];
    }

    public function setDeleteMethod(string $deleteMethod): self
    {
        $this->methods[self::CRUD_DELETE] = $deleteMethod;
        return $this;
    }

    /**
     * Builds edit form for controllers
     */
    public function getEditForm(
        string $uri,
        string $formType,
        $data,
        array $formOptions = [],
        int $submit = self::SUBMIT_STANDARD
    ): FormInterface {
        $formBuilder = $this->getFormBuilder($uri, self::CRUD_UPDATE, $formType, $data, $formOptions, $submit);

        return $formBuilder->getForm();
    }

    public function getEditMethod(): string
    {
        return $this->methods[self::CRUD_UPDATE];
    }

    public function setEditMethod(string $editMethod): self
    {
        $this->methods[self::CRUD_UPDATE] = $editMethod;
        return $this;
    }

    /**
     * Create createForm for controllers.
     */
    public function getCreateForm(
        string $uri,
        string $formType,
        $data,
        array $formOptions = [],
        int $submit = self::SUBMIT_STANDARD
    ): FormInterface {
        $formBuilder = $this->getFormBuilder($uri, self::CRUD_CREATE, $formType, $data, $formOptions, $submit);

        return $formBuilder->getForm();
    }

    public function getCreateMethod(): string
    {
        return $this->methods[self::CRUD_CREATE];
    }

    public function setCreateMethod(string $createMethod): self
    {
        $this->methods[self::CRUD_CREATE] = $createMethod;
        return $this;
    }

    public function getFormBuilder(
        string $action,
        string $method,
        ?string $formType,
        $data = null,
        array $formOptions = [],
        int $submit = self::SUBMIT_STANDARD
    ): FormBuilderInterface {
        $submitOptions = $this->getSubmitOptions($submit);

        $formType = is_null($formType) ? FormType::class : $formType;

        $formOptions = array_merge($this->defaultFormOptions, $formOptions);

        $formBuilder = $this
            ->getFormFactory()
            ->createBuilder($formType, $data, $formOptions);

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
     * Sets submit options from given submit mode.
     */
    private function getSubmitOptions(int $submitMask): array
    {
        $submitOptions = [];

        if (self::SUBMIT_PREVIEW === ($submitMask & self::SUBMIT_PREVIEW)) {
            $submitOptions[self::SUBMIT_PREVIEW] = [
                'attr' => ['class' => 'btn-success btn-submit btn-submit-preview'],
                'label' => 'admin.button.submit_preview',
            ];
        }

        if (self::SUBMIT_CLOSE === ($submitMask & self::SUBMIT_CLOSE)) {
            $submitOptions[self::SUBMIT_CLOSE] = [
                'attr' => ['class' => 'btn-success btn-submit btn-submit-close'],
                'label' => 'admin.button.submit_close',
            ];
        }

        if (self::SUBMIT_STANDARD === ($submitMask & self::SUBMIT_STANDARD)) {
            $submitOptions[self::SUBMIT_STANDARD] = [
                'attr' => ['class' => 'btn-success btn-submit'],
                'label' => 'admin.button.submit',
            ];
        }

        if (self::SUBMIT_DELETE === ($submitMask & self::SUBMIT_DELETE)) {
            $submitOptions[self::SUBMIT_DELETE] = [
                'attr' => ['class' => 'btn-danger btn-delete'],
                'label' => 'admin.button.delete',
            ];
        }

        return $submitOptions;
    }
}
