<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\EventListener\Gedmo;

use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class TranslatableLocaleListener
 * @package Nfq\AdminBundle\EventListener\Gedmo
 */
class TranslatableLocaleListener
{
    /** @var TranslatableListener */
    private $translatable;

    public function __construct(TranslatableListener $translatable)
    {
        $this->translatable = $translatable;
    }

    public function onLateKernelRequest(GetResponseEvent $event): void
    {
        if (null === $this->translatable) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->attributes->has('_nfq_admin')) {
            return;
        }

        $translatableLocale = $this->getTranslatableLocaleFromSubmittedForm($request);
        if (!$translatableLocale) {
            $translatableLocale = $request->getLocale();
        }

        $this->translatable->setTranslatableLocale($translatableLocale);
    }

    private function getTranslatableLocaleFromSubmittedForm(Request $request): ?string
    {
        $translatableLocale = null;

        if ($request->isMethod('POST') && $request->request->has('submitLocale')) {
            return $request->request->get('submitLocale');
        }

        return null;
    }
}
