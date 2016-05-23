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
     * @var ObjectRepository
     */
    protected $er;

    /**
     * @var GenericSearchInterface
     */
    protected $search;

    /**
     * @var GenericActionsInterface
     */
    protected $actions;

    /**
     * @inheritdoc
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @inheritdoc
     */
    public function setSearch(GenericSearchInterface $search)
    {
        $this->search = $search;
    }

    /**
     * @param $entity
     * @param string $beforeEventName
     * @param string $afterEventName
     * @return mixed
     */
    public function delete(
        $entity,
        $beforeEventName = 'generic.before_delete',
        $afterEventName = 'generic.after_delete'
    ) {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'general.deleted_successfully');

        $this->actions->delete($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    /**
     * @param $entity
     * @param string $beforeEventName
     * @param string $afterEventName
     * @return mixed
     */
    public function insert(
        $entity,
        $beforeEventName = 'generic.before_insert',
        $afterEventName = 'generic.after_insert'
    ) {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'general.saved_successfully');

        $this->actions->save($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    /**
     * @param $entity
     * @param string $beforeEventName
     * @param string $afterEventName
     * @return mixed
     */
    public function save(
        $entity,
        $beforeEventName = 'generic.before_save',
        $afterEventName = 'generic.after_save'
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
