<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RouteCheckRedirectSubscriber implements EventSubscriberInterface
{
    /**
     * Handles NotFoundHttpException globally by redirecting unknown routes to the home page.
     *
     * If the requested path does not exist and is not the root or a login path, sets a flash message
     * and redirects the user to the home page. Skips the flash message for login-related redirects.
     *
     * @param ExceptionEvent $event The event containing the exception and request context
     * @return void
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        $path = $request->getPathInfo();
        $referer = $request->headers->get('referer');
        // List of login-related paths to ignore for flash message.
        $loginPaths = ['/login'];
        $isLoginPath = in_array($path, $loginPaths);
        $isFromLogin = false;
        if ($referer) {
            $refererPath = parse_url($referer, PHP_URL_PATH);
            if (in_array($refererPath, $loginPaths)) {
                $isFromLogin = true;
            }
        }
        // Only redirect if it's a route not found (NotFoundHttpException) and not already at root.
        if ($exception instanceof NotFoundHttpException && $path !== '/') {
            // Set flash message in session, except for login redirects
            if (!$isLoginPath && !$isFromLogin && $request->hasSession()) {
                $request->getSession()->getFlashBag()->add('notice', 'Putanja koju ste izabrali: <b>"'
                    . $path .'"</b> ne postoji. Vi ste vraćeni na početnu stranicu.');
            }
            $event->setResponse(new RedirectResponse('/'));
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
