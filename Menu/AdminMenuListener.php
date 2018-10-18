<?php declare(strict_types=1);

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
    /** @var FactoryInterface */
    protected $factory;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var string[]|null */
    protected $grantedRoles;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $grantedRoles = $this->getGrantedRoles();

        if (null === $grantedRoles
            || (!empty($grantedRoles) && $this->authorizationChecker->isGranted($grantedRoles))) {
            $this->setFactory($event);
            $this->doMenuConfigure($event);
        }
    }

    abstract protected function doMenuConfigure(ConfigureMenuEvent $event): void;

    protected function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    private function setFactory(ConfigureMenuEvent $event): void
    {
        $this->factory = $event->getFactory();
    }

    /**
     * @return string[]|null
     */
    private function getGrantedRoles(): ?array
    {
        return $this->grantedRoles;
    }

    /**
     * @param string[] $grantedRoles
     */
    public function setGrantedRoles(array $grantedRoles): void
    {
        $this->grantedRoles = $grantedRoles;
    }
}
