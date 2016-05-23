<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Nfq\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AdminMenuListener
 * @package Nfq\AdminBundle\Menu
 */
abstract class AdminMenuListener implements AdminMenuListenerInterface
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var array Granted roles
     */
    protected $grantedRoles = [];

    /**
     * AdminMenuListener constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $grantedRoles = $this->getGrantedRoles();

        if (empty($grantedRoles) || $this->authorizationChecker->isGranted($grantedRoles)) {
            $this->setFactory($event);
            $this->doMenuConfigure($event);
        }
    }

    /**
     * @param ConfigureMenuEvent $event
     *
     * @return mixed
     */
    abstract protected function doMenuConfigure(ConfigureMenuEvent $event);

    /**
     * @return FactoryInterface
     */
    protected function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    private function setFactory(ConfigureMenuEvent $event)
    {
        $this->factory = $event->getFactory();
    }

    /**
     * @return array
     */
    private function getGrantedRoles()
    {
        return $this->grantedRoles;
    }

    /**
     * @param array $grantedRoles
     */
    public function setGrantedRoles($grantedRoles)
    {
        $this->grantedRoles = $grantedRoles;
    }
}
