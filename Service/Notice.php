<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service;

use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Session\Session;
use Nfq\AdminBundle\Notices;

/**
 * Class Notice
 * @package Nfq\AdminBundle\Service
 */
class Notice implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $message
     * @param string $flag
     */
    public function add($message = "", $flag = Notices::NOTICE_INFO)
    {
        try {
            $this->session->getFlashBag()->add($flag, $message);
        } catch (\RuntimeException $ex) {
            //Might fail due to headers_sent or session failed to start
            //So just do not add the message to the flash bag and log the exception instead
            $this->logger->warning($ex->getMessage());
        }
    }

    /**
     * @param $message
     */
    public function addInfo($message)
    {
        $this->add($message, Notices::NOTICE_INFO);
    }

    /**
     * @param $message
     */
    public function addWarning($message)
    {
        $this->add($message, Notices::NOTICE_WARNING);
    }

    /**
     * @param $message
     */
    public function addDanger($message)
    {
        $this->add($message, Notices::NOTICE_DANGER);
    }

    /**
     * @param $message
     */
    public function addSuccess($message)
    {
        $this->add($message, Notices::NOTICE_SUCCESS);
    }
}
