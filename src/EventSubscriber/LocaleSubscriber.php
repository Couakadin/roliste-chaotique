<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{

    public function __construct() {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if(!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a cookie
        if($locale = $request->cookies->get('locale')) {
            $request->setLocale($locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getDefaultLocale());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
