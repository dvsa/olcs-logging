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
        $mockConfig->setBlackListedMethods(
            [
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
            ]
        );
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


    /**
     * @param string $content
     * @param boolean $shouldLogContent
     * @dataProvider httpOnDispatchProvider
     */
    public function testHttpOnDispatch($content, $shouldLogContent)
    {
        $route = ['controller' => 'index', 'action' => 'index'];
        $query = [];
        $post = [];
        $method = 'GET';
        $path = '/';
        $headers = [];

        $expectedData = [
            'path' => $path,
            'method' => $method,
            'route_params' => $route,
            'get' => $query,
            'post' => $post,
            'headers' => $headers,
        ];
        if ($shouldLogContent) {
            $expectedData['content'] = $content;
        }

        $mockRequest = m::mock('Zend\Http\Request');
        $mockRequest->shouldReceive('getQuery')->andReturn($query);
        $mockRequest->shouldReceive('getUri->__toString')->andReturn($path);
        $mockRequest->shouldReceive('getMethod')->andReturn($method);
        $mockRequest->shouldReceive('getPost')->andReturn($post);
        $mockRequest->shouldReceive('getHeaders->toArray')->andReturn($headers);
        $mockRequest->shouldReceive('getContent')->andReturn($content);

        $mockRequest
            ->shouldReceive('getHeader')
            ->with('Content-Length')
            ->andReturn(
                m::mock(\Zend\Http\Header\ContentLength::class)
                    ->shouldReceive('getFieldValue')
                    ->andReturn(strlen($content))
                    ->getMock()
            );

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockEvent->shouldReceive('getRouteMatch->getParams')->andReturn($route);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->with(
            'Request received',
            [
                'data' => $expectedData,
            ]
        );

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatch($mockEvent);
    }

    public function httpOnDispatchProvider()
    {
        return [
            'acceptable content size' => [
                'content' => 'foo',
                'shouldLogContent' => true,
            ],
            'content too large' => [
                'content' => str_pad('foo', 3000),
                'shouldLogContent' => false,
            ],
        ];
    }

    public function testHttpOnDispatchEnd()
    {
        $params = ['code' => '200', 'status' => 'OK'];

        $mockResponse = m::mock('Zend\Http\Response');
        $mockResponse->shouldReceive('getStatusCode')->andReturn('200');
        $mockResponse->shouldReceive('getReasonPhrase')->andReturn('OK');

        $mockRequest = m::mock('Zend\Http\Request');

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getResponse')->andReturn($mockResponse);
        $mockEvent->shouldReceive('getRequest')->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->with('Request completed', ['data' => $params]);

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatchEnd($mockEvent);
    }

    public function testConsoleOnDispatch()
    {
        $scriptName = 'file.php';
        $params = ['route-name', '--help'];

        $mockRequest = m::mock('Zend\Console\Request');
        $mockRequest->shouldReceive('getScriptName')->andReturn($scriptName);
        $mockRequest->shouldReceive('getParams')->andReturn($params);

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->with(
            'Request received',
            [
                'data' => [
                    'path' => $scriptName,
                    'params' => $params,
                ]
            ]
        );

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatch($mockEvent);
    }

    public function testConsoleOnDispatchEnd()
    {
        $params = ['code' => '200', 'status' => 'OK'];

        $mockRequest = m::mock('Zend\Console\Request');

        $mockEvent = m::mock('Zend\Mvc\MvcEvent');
        $mockEvent->shouldNotReceive('getResponse');
        $mockEvent->shouldReceive('getRequest')->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldNotReceive('info');

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatchEnd($mockEvent);
    }
}
