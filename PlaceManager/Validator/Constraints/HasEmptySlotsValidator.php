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

use Nfq\AdminBundle\PlaceManager\PlaceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class HasEmptySlotsValidator
 * @package Nfq\AdminBundle\PlaceManager\Validator\Constraints
 */
class HasEmptySlotsValidator extends ConstraintValidator implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $placeManager = $this->getPlaceManager($constraint->manager);

        foreach($value as $slotId) {
            if (false === $placeManager->hasEmptySlots($slotId)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%limit%', $placeManager->getPlace($slotId)['limit'])
                    ->addViolation();
            }
        }
    }

    /**
     * @param $manager
     * @return PlaceManagerInterface
     */
    private function getPlaceManager($manager)
    {
        if (!$this->container->has($manager)) {
            throw new \InvalidArgumentException(sprintf('Given place manager `%s` does not exist', $manager));
        }

        return $this->container->get($manager);
    }
}
