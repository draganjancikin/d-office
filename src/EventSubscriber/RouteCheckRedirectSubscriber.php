<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RouteCheckRedirectSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        // Only redirect if it's a route not found (NotFoundHttpException) and not already at root
        if ($exception instanceof NotFoundHttpException && $request->getPathInfo() !== '/') {
            // Set flash message in session
            if ($request->hasSession()) {
                $request->getSession()->getFlashBag()->add('notice', 'Putanja koju ste izabrali: <b>"'
                    . $request->getPathInfo() .'"</b> ne postoji. Vi ste vraćeni na početnu stranicu.');
            }
            $event->setResponse(new RedirectResponse('/'));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
