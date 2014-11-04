<?php


namespace OlcsTest\Logging\Listener;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Olcs\Logging\Listener\LogRequest;
use Mockery as m;
use Zend\Mvc\MvcEvent;

/**
 * Class LogRequestTest
 * @package OlcsTest\Logging\Listener
 */
class LogRequestTest extends TestCase
{
    /**
     * @return m\MockInterface
     */
    protected function getMockLog()
    {
        $mockConfig = new m\Generator\MockConfigurationBuilder();
        $mockConfig->setBlackListedMethods(array(
            '__call',
            '__callStatic',
            '__clone',
            '__wakeup',
            '__set',
            '__get',
            '__toString',
            '__isset',

            // below are reserved words in PHP
            "__halt_compiler", "abstract", "and", "array", "as",
            "break", "callable", "case", "catch", "class",
            "clone", "const", "continue", "declare", "default",
            "die", "do", "echo", "else", "elseif",
            "empty", "enddeclare", "endfor", "endforeach", "endif",
            "endswitch", "endwhile", "eval", "exit", "extends",
            "final", "for", "foreach", "function", "global",
            "goto", "if", "implements", "include", "include_once",
            "instanceof", "insteadof", "interface", "isset", "list",
            "namespace", "new", "or", "print", "private",
            "protected", "public", "require", "require_once", "return",
            "static", "switch", "throw", "trait", "try",
            "unset", "use", "var", "while", "xor"
        ));
        $mockConfig->addTarget('Zend\Log\Logger');

        $mockLog = m::mock($mockConfig);
        $mockLog->shouldReceive('__destruct');
        return $mockLog;
    }

    public function testAttach()
    {
        $sut = new LogRequest();

        $mockEvents = m::mock('Zend\EventManager\EventManagerInterface');
        $mockEvents->shouldReceive('attach')
            ->with(MvcEvent::EVENT_ROUTE, array($sut, 'onDispatch'), 10000);
        $mockEvents->shouldReceive('attach')
            ->with(MvcEvent::EVENT_FINISH, array($sut, 'onDispatchEnd'), 10000);

        $sut->attach($mockEvents);
    }

    public function testCreateService()
    {
        $mockLog = $this->getMockLog();

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($mockLog);

        $sut = new LogRequest();
        $service = $sut->createService($mockSl);

        $this->assertSame($sut, $service);
        $this->assertSame($mockLog, $service->getLogger());
    }

    public function testOnDispatch()
    {
        $route = ['controller' => 'index', 'action' => 'index'];
        $query = [];
        $post = [];
        $method = 'GET';
        $path = '/';
        $headers = [];

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getRouteMatch->getParams')->andReturn($route);
        $mockEvent->shouldReceive('getRequest->getQuery')->andReturn($query);
        $mockEvent->shouldReceive('getRequest->getUri->__toString')->andReturn($path);
        $mockEvent->shouldReceive('getRequest->getMethod')->andReturn($method);
        $mockEvent->shouldReceive('getRequest->getPost')->andReturn($post);
        $mockEvent->shouldReceive('getRequest->getHeaders->toArray')->andReturn($headers);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->with(
            'Request recieved',
            [
                'data' => [
                    'path' => $path,
                    'method' => $method,
                    'route_params' => $route,
                    'get' => $query,
                    'post' => $post,
                    'headers' => $headers
                ]
            ]
        );

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatch($mockEvent);
    }

    public function testOnDispatchEnd()
    {
        $params = ['code' => '200', 'status' => 'OK'];

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getResponse->getStatusCode')->andReturn('200');
        $mockEvent->shouldReceive('getResponse->getReasonPhrase')->andReturn('OK');

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->with('Request completed', ['data' => $params]);

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatchEnd($mockEvent);
    }
}
 