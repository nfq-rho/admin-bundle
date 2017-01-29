<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Tests\Functional\EventListener;

use Behat\Symfony2Extension\Context\Initializer\KernelAwareInitializer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Nfq\AdminBundle\EventListener\LocaleListener;

/**
 * Class LocaleListenerTest
 * @package Nfq\AdminBundle\Tests\Functional\EventListener
 */
class LocaleListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $kernel;

    /** @var  Session */
    protected $session;

    public function setUp()
    {
        $this->kernel = $this->createMock(HttpKernelInterface::class);
        /**
         *
         */
        $this->session = new Session(new MockFileSessionStorage());
    }

    public function testOnKernelRequestWithoutParams()
    {
        $defaultLocale = 'en_EN';
        $request = new Request();
        $request->setDefaultLocale($defaultLocale);
        $this->session->setName('session_name');
        $request->setSession($this->session);
        $request->cookies->set('session_name', 'name');
        $event = new GetResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener = new LocaleListener($defaultLocale);
        $listener->onKernelRequest($event);

        $this->assertEquals($defaultLocale, $event->getRequest()->getLocale());

        $newLocale = 'pl';
        $param = new ParameterBag();
        $param->set('_locale', $newLocale);
        $event->getRequest()->query = $param;
        $listener = new LocaleListener($defaultLocale);
        $listener->onKernelRequest($event);
        $this->assertEquals($newLocale, $this->session->get('_locale'));
        $this->assertEquals($newLocale, $event->getRequest()->getLocale());

        $locale = 'en';
        $param = new ParameterBag();
        $event->getRequest()->query = $param;
        $event->getRequest()->getSession()->clear();
        $event->getRequest()->getSession()->set('_locale', $locale);

        $listener = new LocaleListener($defaultLocale);
        $listener->onKernelRequest($event);
        $this->assertEquals($locale, $event->getRequest()->getSession()->get('_locale'));
        $this->assertEquals($locale, $event->getRequest()->getLocale());

    }
}
