<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class AuthenticationController
 * @package Nfq\AdminBundle\Controller
 */
class AuthenticationController extends Controller
{
    /**
     * @Template()
     * @param Request $request
     * @return array
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return [
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error' => $error,
        ];
    }

    /**
     * @return void
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function logoutAction(Request $request)
    {
        $request->getSession()->clear();
        $uri = $this->generateUrl('admin_login', [], UrlGenerator::ABSOLUTE_URL);
        return $this->redirect($uri);
    }
}
