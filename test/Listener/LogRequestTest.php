<?php

namespace OlcsTest\Logging\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request;
use Laminas\Log\Logger;
use Laminas\Router\Http\RouteMatch;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Olcs\Logging\CliLoggableInterface;
use Olcs\Logging\Listener\LogRequest;
use Mockery as m;
use Laminas\Mvc\MvcEvent;
use Psr\Container\ContainerInterface;

class LogRequestTest extends TestCase
{
    protected function getMockLog()
    {
        return m::mock(Logger::class);
    }

    public function testAttach()
    {
        $sut = new LogRequest();

        $mockEvents = m::mock(EventManagerInterface::class);
        $mockEvents->shouldReceive('attach')->atLeast()->once()
            ->with(MvcEvent::EVENT_ROUTE, array($sut, 'onRoute'), 10000);

        $mockEvents->shouldReceive('attach')->atLeast()->once()
            ->with(MvcEvent::EVENT_DISPATCH, array($sut, 'onDispatch'), 10000);

        $mockEvents->shouldReceive('attach')->atLeast()->once()
            ->with(MvcEvent::EVENT_FINISH, array($sut, 'onDispatchEnd'), 10000);

        $sut->attach($mockEvents);
    }

    public function testInvoke(): void
    {
        $mockLog = $this->getMockLog();

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($mockLog);

        $sut = new LogRequest();
        $service = $sut->__invoke($mockSl, LogRequest::class);

        $this->assertSame($sut, $service);
        $this->assertSame($mockLog, $service->getLogger());
    }


    /**
     * @param string $content
     * @param boolean $shouldLogContent
     * @dataProvider httpOnDispatchProvider
     */
    public function testHttpOnRoute($content, $shouldLogContent)
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
        } else {
            $expectedData['content'] = 'MAX_CONTENT_LENGTH_TO_LOG exceeded';
        }

        $mockRequest = m::mock('Laminas\Http\Request');
        $mockRequest->shouldReceive('getQuery->getArrayCopy')->andReturn($query);
        $mockRequest->shouldReceive('getUri->__toString')->andReturn($path);
        $mockRequest->shouldReceive('getMethod')->andReturn($method);
        $mockRequest->shouldReceive('getPost->getArrayCopy')->andReturn($post);
        $mockRequest->shouldReceive('getHeaders->toArray')->andReturn($headers);
        $mockRequest->shouldReceive('getContent')->andReturn($content);

        $mockRequest
            ->shouldReceive('getHeader')
            ->with('Content-Length')
            ->andReturn(
                m::mock(\Laminas\Http\Header\ContentLength::class)
                    ->shouldReceive('getFieldValue')
                    ->andReturn(strlen($content))
                    ->getMock()
            );

        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockEvent->shouldReceive('getRouteMatch->getParams')->atLeast()->once()->andReturn($route);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('debug')->with(
            'Request received',
            [
                'data' => $expectedData,
            ]
        );

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onRoute($mockEvent);
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
        $params = ['request' => 'http://foo.com/bar', 'code' => '200', 'status' => 'OK'];

        $mockResponse = m::mock('Laminas\Http\Response');
        $mockResponse->shouldReceive('getStatusCode')->andReturn('200');
        $mockResponse->shouldReceive('getReasonPhrase')->andReturn('OK');
        $mockResponse->shouldReceive('isServerError')->andReturn(false);
        $mockResponse->shouldReceive('isClientError')->andReturn(false);

        $mockRequest = m::mock('Laminas\Http\Request');
        $mockRequest->shouldReceive('getUriString')->andReturn('http://foo.com/bar');

        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getResponse')->andReturn($mockResponse);
        $mockEvent->shouldReceive('getRequest')->atLeast()->once()->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('debug')->with('Request completed', ['data' => $params]);

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatchEnd($mockEvent);
    }

    public function testHttpOnDispatchEndClientError()
    {
        $params = ['request' => 'http://foo.com/bar', 'code' => '403', 'status' => 'Foo'];

        $mockResponse = m::mock('Laminas\Http\Response');
        $mockResponse->shouldReceive('getStatusCode')->andReturn('403');
        $mockResponse->shouldReceive('getReasonPhrase')->andReturn('Foo');
        $mockResponse->shouldReceive('isServerError')->andReturn(false);
        $mockResponse->shouldReceive('isClientError')->andReturn(true);

        $mockRequest = m::mock('Laminas\Http\Request');
        $mockRequest->shouldReceive('getUriString')->andReturn('http://foo.com/bar');

        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getResponse')->andReturn($mockResponse);
        $mockEvent->shouldReceive('getRequest')->atLeast()->once()->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->with('Request completed', ['data' => $params]);

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatchEnd($mockEvent);
    }

    public function testHttpOnDispatchEndServerError()
    {
        $params = ['request' => 'http://foo.com/bar', 'code' => '500', 'status' => 'Foo'];

        $mockResponse = m::mock('Laminas\Http\Response');
        $mockResponse->shouldReceive('getStatusCode')->andReturn('500');
        $mockResponse->shouldReceive('getReasonPhrase')->andReturn('Foo');
        $mockResponse->shouldReceive('isServerError')->andReturn(true);
        $mockResponse->shouldReceive('isClientError')->andReturn(false);
        $mockResponse->shouldReceive('getBody')->andReturn('BODY');

        $mockRequest = m::mock('Laminas\Http\Request');
        $mockRequest->shouldReceive('getUriString')->andReturn('http://foo.com/bar');

        $mockEvent = m::mock('Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getResponse')->andReturn($mockResponse);
        $mockEvent->shouldReceive('getRequest')->atLeast()->once()->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('err')->with('Request completed', ['data' => $params]);

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatchEnd($mockEvent);
    }

    public function testHttpOnDispatch()
    {
        $mockController = m::mock();

        $params = [
            'controller' => get_class($mockController),
            'action' => 'foo'
        ];

        $mockRequest = m::mock(Request::class);

        $routeMatch = m::mock();
        $routeMatch->shouldReceive('getParam')->with('controller')->andReturn('ControllerAlias');
        $routeMatch->shouldReceive('getParam')->with('action')->andReturn('foo');

        $mockEvent = m::mock(MvcEvent::class);
        $mockEvent->shouldReceive('getRouteMatch')->andReturn($routeMatch);
        $mockEvent->shouldReceive('getApplication->getServiceManager->get->get')->with('ControllerAlias')
            ->andReturn($mockController);

        $mockEvent->shouldReceive('getRequest')->atLeast()->once()->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('debug')->with('Request dispatched', ['data' => $params]);

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatch($mockEvent);
    }

    public function testConsoleOnDispatch()
    {
        $mockRequest = m::mock(CliLoggableInterface::class);
        $mockRequest->shouldReceive('getScriptPath')->andReturn('file.php');
        $mockRequest->shouldReceive('getScriptParams')->andReturn(['file.php', 'route-name', '--help']);

        $mockEvent = m::mock(MvcEvent::class);
        $mockEvent->shouldReceive('getRequest')->atLeast()->once()->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('debug')->with(
            'Request received',
            [
                'data' => [
                    'path' => 'file.php',
                    'params' => ['file.php', 'route-name', '--help'],
                ]
            ]
        );

        $mockRouteMatch = m::mock(RouteMatch::class);
        $mockEvent->shouldReceive('getRouteMatch')->andReturn($mockRouteMatch);
        $mockRequest->shouldReceive('getUri')->andReturn('uri');
        $mockRouteMatch->shouldReceive('getMatchedRouteName')->andReturn('route-name');

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onRoute($mockEvent);
    }

    public function testConsoleOnDispatchEnd()
    {
        $mockRequest = m::mock(CliLoggableInterface::class);

        $mockRequest->shouldReceive('getScriptPath')->andReturn('file.php');

        $mockEvent = m::mock(MvcEvent::class);
        $mockEvent->shouldNotReceive('getResponse');
        $mockEvent->shouldReceive('getRequest')->atLeast()->once()->andReturn($mockRequest);

        $mockLog = $this->getMockLog();
        $mockLog->shouldNotReceive('debug');

        $sut = new LogRequest();
        $sut->setLogger($mockLog);
        $sut->onDispatchEnd($mockEvent);
    }
}
