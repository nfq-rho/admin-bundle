<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service\Admin;

use Doctrine\ORM\EntityRepository;
use Nfq\AdminBundle\Event\GenericEvent;
use Nfq\AdminBundle\Service\Generic\Search\GenericSearchInterface;
use Nfq\AdminBundle\Service\Generic\Actions\GenericActionsInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Class AbstractAdminManager
 * @package Nfq\AdminBundle\Service\Admin
 */
abstract class AbstractAdminManager implements AdminManagerInterface
{
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var GenericSearchInterface
     */
    protected $search;

    /**
     * @var GenericActionsInterface
     */
    protected $actions;

    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function setActions(GenericActionsInterface $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @inheritdoc
     */
    public function setSearch(GenericSearchInterface $search): void
    {
        $this->search = $search;
    }

    public function delete(
        $entity,
        string $beforeEventName = 'generic.before_delete',
        string $afterEventName = 'generic.after_delete'
    ) {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'general.deleted_successfully');

        $this->actions->delete($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    public function insert(
        $entity,
        string $beforeEventName = 'generic.before_insert',
        string $afterEventName = 'generic.after_insert'
    ) {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'general.saved_successfully');

        $this->actions->save($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    public function save(
        $entity,
        string $beforeEventName = 'generic.before_save',
        string $afterEventName = 'generic.after_save'
    ) {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'general.saved_successfully');

        $this->actions->save($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getResults(Request $request)
    {
        $request->request->add([
            'sort' => null !== ($sort = $request->get('sort')) ? $sort : false,
            'by' => null !== ($sort = $request->get('direction')) ? $sort : false,
        ]);

        return $this->search->getResults($request);
    }
}
