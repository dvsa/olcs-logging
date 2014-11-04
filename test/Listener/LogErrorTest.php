<?php


namespace OlcsTest\Logging\Listener;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\Logging\Listener\LogError;
use Zend\Mvc\MvcEvent;

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
            ->with(MvcEvent::EVENT_DISPATCH_ERROR, array($sut, 'onDispatchError'),10000);

        $sut->attach($mockEvents);
    }

    public function testCreateService()
    {
        $mockHelper = m::mock('Olcs\Logging\Helper\LogException');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Olcs\Logging\Helper\LogException')->andReturn($mockHelper);

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

        $mockHelper = m::mock('Olcs\Logging\Helper\LogException');
        $mockHelper->shouldReceive('logException')->with($exception, ['data' => $params]);

        $sut = new LogError();
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
 