<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\PlaceManager\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class HasEmptySlots
 * @package Nfq\AdminBundle\PlaceManager\Validator\Constraints
 * @Annotation
 */
class HasEmptySlots extends Constraint
{
    public $message = 'nfq_admin.validator.place.all_available_slots_used';

    /**
     * @var string
     */
    public $manager;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['manager'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'nfq_admin_validator_has_empty_slots';
    }
}
