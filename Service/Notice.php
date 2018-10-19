<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service;

use Nfq\AdminBundle\Notices;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Notice
 * @package Nfq\AdminBundle\Service
 */
class Notice
{
    public const NOTICE_SUCCESS = 'success';
    public const NOTICE_INFO = 'info';
    public const NOTICE_WARNING = 'warning';
    public const NOTICE_DANGER = 'danger';

    /** @var SessionInterface */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function add(string $message, string $flag = self::NOTICE_INFO): void
    {
        $this->session->getFlashBag()->add($flag, $message);
    }

    public function addInfo(string $message): void
    {
        $this->add($message, self::NOTICE_INFO);
    }

    public function addWarning(string $message): void
    {
        $this->add($message, self::NOTICE_WARNING);
    }

    public function addDanger(string $message): void
    {
        $this->add($message, self::NOTICE_DANGER);
    }

    public function addSuccess(string $message): void
    {
        $this->add($message, self::NOTICE_SUCCESS);
    }
}
