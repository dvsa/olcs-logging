<?php


namespace OlcsTest\Logging\Listener;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\Logging\Listener\LogError;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Class LogErrorTest
 * @package OlcsTest\Logging\Listener
 */
class LogErrorTest extends TestCase
{
    public function testAttach()
    {
        $sut = new LogError();

        $mockEvents = m::mock('Zend\EventManager\EventManagerInterface');
        $mockEvents->shouldReceive('attach')
            ->with(MvcEvent::EVENT_DISPATCH_ERROR, array($sut, 'onDispatchError'), 0);
        $mockEvents->shouldReceive('attach')
            ->with(MvcEvent::EVENT_RENDER_ERROR, array($sut, 'onDispatchError'), 0);

        $sut->attach($mockEvents);
    }

    public function testCreateService()
    {
        $mockHelper = m::mock('Olcs\Logging\Helper\LogException');

        $mockLogProcessorManager = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockLogProcessorManager->shouldReceive('get->getIdentifier')->with()->once()->andReturn('IDENTIFER');
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Olcs\Logging\Helper\LogException')->andReturn($mockHelper);
        $mockSl->shouldReceive('get')->with('LogProcessorManager')->once()->andReturn($mockLogProcessorManager);

        $sut = new LogError();
        $service = $sut->createService($mockSl);

        $this->assertSame($sut, $service);
        $this->assertSame($mockHelper, $service->getLogExceptionHelper());
    }

    public function testOnDispatchError()
    {
        $exception = new \Exception();
        $params = ['controller' => 'index', 'action' => 'index'];

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getParam')->with('exception')->andReturn($exception);
        $mockEvent->shouldReceive('getRouteMatch->getParams')->andReturn($params);
        $mockEvent->shouldReceive('getResult')->with()->once()->andReturn('FOO');

        $mockHelper = m::mock('Olcs\Logging\Helper\LogException');
        $mockHelper->shouldReceive('logException')->with($exception, ['data' => $params]);

        $sut = new LogError();
        $sut->setLogExceptionHelper($mockHelper);

        $sut->onDispatchError($mockEvent);
    }

    public function testOnDispatchErrorViewModel()
    {
        $exception = new \Exception();
        $params = ['controller' => 'index', 'action' => 'index'];

        $mockView = m::mock(ViewModel::class);
        $mockView->shouldReceive('setVariable')->with('id', 'IDENTIFIER')->once();

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getParam')->with('exception')->andReturn($exception);
        $mockEvent->shouldReceive('getRouteMatch->getParams')->andReturn($params);
        $mockEvent->shouldReceive('getResult')->with()->atLeast(1)->andReturn($mockView);

        $mockHelper = m::mock('Olcs\Logging\Helper\LogException');
        $mockHelper->shouldReceive('logException')->with($exception, ['data' => $params]);

        $sut = new LogError();
        $sut->setIdentifier('IDENTIFIER');
        $sut->setLogExceptionHelper($mockHelper);

        $sut->onDispatchError($mockEvent);
    }

    public function testOnDispatchErrorNoException()
    {
        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getParam')->with('exception')->andReturn(null);

        $sut = new LogError();

        $sut->onDispatchError($mockEvent);
    }
}
