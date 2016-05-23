<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Tests\Functional\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Nfq\AdminBundle\Service\Notice;

/**
 * Class NoticeTest
 * @package Nfq\AdminBundle\Tests\Functional\Service
 */
class NoticeTest extends \PHPUnit_Framework_TestCase
{

    public function testAddInfo()
    {
        $session = new Session(new MockFileSessionStorage());
        $notice = new Notice($session);
        $notice->addInfo('test_info_message');

        $this->assertEquals(array('test_info_message'), $session->getFlashBag()->get('info'));
    }


    public function testAddWarning()
    {
        $session = new Session(new MockFileSessionStorage());
        $notice = new Notice($session);
        $notice->addWarning('test_warning_message');
        $this->assertEquals(array('test_warning_message'), $session->getFlashBag()->get('warning'));
    }


    public function testAddDanger()
    {
        $session = new Session(new MockFileSessionStorage());
        $notice = new Notice($session);
        $notice->addDanger('test_danger_message');
        $this->assertEquals(array('test_danger_message'), $session->getFlashBag()->get('danger'));
    }

    public function testAddSuccess()
    {
        $session = new Session(new MockFileSessionStorage());
        $notice = new Notice($session);
        $notice->addSuccess('test_success_message');
        $this->assertEquals(array('test_success_message'), $session->getFlashBag()->get('success'));
    }
}
