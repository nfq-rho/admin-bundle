<?php

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
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ResponseListener
 * @package Nfq\AdminBundle\EventListener
 */
class ResponseListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [['onKernelResponse', 10]],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        //This listener does only apply when in modal mode
        if (!$request->query->has('isModal')) {
            return;
        }

        //Handle error responses
        if ($response->getStatusCode() === 500) {
            $response->setStatusCode(200);
            return;
        }

        $modalResponse = new JsonResponse();

        if ($response instanceof RedirectResponse) {
            $this->setTargetPath($event);

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

    /**
     * @param FilterResponseEvent $event
     */
    private function setTargetPath(FilterResponseEvent $event)
    {
        $event->getRequest()->getSession()->set('_security.admin_area.target_path',
            $event->getRequest()->server->get('HTTP_REFERER'));
    }
}
