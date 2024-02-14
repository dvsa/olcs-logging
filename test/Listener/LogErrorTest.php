<?php

namespace OlcsTest\Logging\Listener;

use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\Logging\Helper\LogException;
use Olcs\Logging\Listener\LogError;
use Laminas\Mvc\MvcEvent;

class LogErrorTest extends TestCase
{
    public function testAttach()
    {
        $sut = new LogError();

        $mockEvents = m::mock('Laminas\EventManager\EventManagerInterface');
        $mockEvents->shouldReceive('attach')->atLeast()->once()
            ->with(MvcEvent::EVENT_DISPATCH_ERROR, array($sut, 'onDispatchError'), 0);
        $mockEvents->shouldReceive('attach')->atLeast()->once()
            ->with(MvcEvent::EVENT_RENDER_ERROR, array($sut, 'onDispatchError'), 0);

        $sut->attach($mockEvents);
    }

    public function testInvoke(): void
    {
        $mockHelper = m::mock(LogException::class);

        $mockLogProcessorManager = m::mock(ContainerInterface::class);
        $mockLogProcessorManager->shouldReceive('get->getIdentifier')->with()->once()->andReturn('IDENTIFER');
        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Olcs\Logging\Helper\LogException')->andReturn($mockHelper);
        $mockSl->shouldReceive('get')->with('LogProcessorManager')->once()->andReturn($mockLogProcessorManager);

        $sut = new LogError();
        $service = $sut->__invoke($mockSl, LogError::class);

        $this->assertSame($sut, $service);
        $this->assertSame($mockHelper, $service->getLogExceptionHelper());
    }

    public function testOnDispatchError()
    {
        $exception = new \Exception();
        $params = ['controller' => 'index', 'action' => 'index'];

        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getParam')->with('exception')->andReturn($exception);
        $mockEvent->shouldReceive('getParam')->with('exceptionNoLog')->andReturn(null);
        $mockEvent->shouldReceive('getRouteMatch->getParams')->atLeast()->once()->andReturn($params);

        $mockHelper = m::mock(LogException::class);
        $mockHelper->shouldReceive('logException')->with($exception, ['data' => $params]);

        $sut = new LogError();
        $sut->setLogExceptionHelper($mockHelper);

        $sut->onDispatchError($mockEvent);
    }

    public function testOnDispatchErrorViewModel()
    {
        $exception = new \Exception();
        $params = ['controller' => 'index', 'action' => 'index'];

        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getParam')->with('exception')->andReturn($exception);
        $mockEvent->shouldReceive('getParam')->with('exceptionNoLog')->andReturn(null);
        $mockEvent->shouldReceive('getRouteMatch->getParams')->andReturn($params);

        $mockHelper = m::mock(LogException::class);
        $mockHelper->shouldReceive('logException')->atLeast()->once()->with($exception, ['data' => $params]);

        $sut = new LogError();
        $sut->setIdentifier('IDENTIFIER');
        $sut->setLogExceptionHelper($mockHelper);

        $sut->onDispatchError($mockEvent);
    }

    public function testOnDispatchErrorNoException()
    {
        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getParam')->atLeast()->once()->with('exception')->andReturn(null);
        $mockEvent->shouldReceive('getParam')->with('exceptionNoLog')->andReturn(null);

        $sut = new LogError();

        $sut->onDispatchError($mockEvent);
    }

    public function testOnDispatchExceptionNoLog()
    {
        $exception = new \Exception();

        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getParam')->atLeast()->once()->with('exception')->andReturn($exception);
        $mockEvent->shouldReceive('getParam')->with('exceptionNoLog')->andReturn(true);

        $sut = new LogError();

        $sut->onDispatchError($mockEvent);
    }
}
