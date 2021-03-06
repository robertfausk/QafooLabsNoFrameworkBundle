<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\EventListener;

use QafooLabs\Bundle\NoFrameworkBundle\EventListener\ParamConverterListener;
use QafooLabs\Bundle\NoFrameworkBundle\ParamConverter\SymfonyServiceProvider;
use QafooLabs\MVC\TokenContext;
use QafooLabs\MVC\Flash;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ParamConverterListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_converts_parameters()
    {
        $container = new Container;
        $container->set('security.token_storage', \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface'));
        $container->set('security.authorization_checker', \Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface'));
        $serviceProvider = new SymfonyServiceProvider($container);

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $listener = new ParamConverterListener($serviceProvider);

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $method = function(Session $session, TokenContext $context, Flash $flash) {};
        $event = new FilterControllerEvent($kernel, $method, $request, null);

        $listener->onKernelController($event);

        $this->assertTrue($request->attributes->has('flash'));
        $this->assertTrue($request->attributes->has('context'));
        $this->assertTrue($request->attributes->has('session'));
    }
}
