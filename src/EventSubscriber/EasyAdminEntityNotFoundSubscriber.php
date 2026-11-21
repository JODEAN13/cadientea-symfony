<?php

namespace App\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityNotFoundException;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION, priority: 10)]
class EasyAdminEntityNotFoundSubscriber
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof EntityNotFoundException) {
            return;
        }

        $request = $event->getRequest();
        $crudControllerFqcn = $request->attributes->get('crudControllerFqcn');

        if (!is_string($crudControllerFqcn) || $crudControllerFqcn === '') {
            return;
        }

        $url = $this->adminUrlGenerator
            ->unsetAll()
            ->setController($crudControllerFqcn)
            ->setAction('index')
            ->generateUrl();

        $session = $request->hasSession() ? $request->getSession() : null;
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('warning', 'That record was already removed or no longer exists.');
        }

        $event->setResponse(new RedirectResponse($url));
    }
}
