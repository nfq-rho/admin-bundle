<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service\Admin;

use Nfq\AdminBundle\Service\Generic\Actions\GenericActionsInterface;

/**
 * Interface AdminManagerInterface
 * @package Nfq\AdminBundle\Service\Admin
 */
interface AdminManagerInterface
{
    public function setActions(GenericActionsInterface $actions): void;

    public function getRepository();

    public function getEntity($id);

    public function delete(
        $entity,
        string $beforeEventName = 'generic.before_delete',
        string $afterEventName = 'generic.after_delete'
    ): void;

    public function insert(
        $entity,
        string $beforeEventName = 'generic.before_insert',
        string $afterEventName = 'generic.after_insert'
    );

    public function save(
        $entity,
        string $beforeEventName = 'generic.before_save',
        string $afterEventName = 'generic.after_save'
    );
}
