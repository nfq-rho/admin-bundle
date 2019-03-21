<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ModalResponseListener
 * @package Nfq\AdminBundle\EventListener
 */
class ModalResponseListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [['onKernelResponse', 10]],
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$request->attributes->has('_nfq_admin')) {
            return;
        }

        if (!$request->query->has('isModal') || false == $request->query->get('isModal', false)) {
            return;
        }

        //Handle error responses
        if ($response->getStatusCode() === 500) {
            $response->setStatusCode(200);
            return;
        }

        $modalResponse = new JsonResponse();

        if ($response instanceof RedirectResponse) {
            $this->setTargetPath($request);

            $redirectUrl = $response->getTargetUrl();

            $modalResponse->setData([
                'status' => 'redirect',
                'content' => $redirectUrl,
            ]);
        } else {
            $modalResponse->setData([
                'status' => 'success',
                'content' => $response->getContent(),
            ]);
        }

        $event->setResponse($modalResponse);
    }

    protected function setTargetPath(Request $request): void
    {
        if (!$request->hasSession()) {
            return;
        }

        $request->getSession()->set(
            '_security.admin_area.target_path',
            $request->server->get('HTTP_REFERER')
        );
    }
}
