<?php

namespace OlcsTest\Logging\Log\Processor;

use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Stdlib\RequestInterface;
use Olcs\Logging\Log\Processor\CorrelationId;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

class CorrelationIdTest extends TestCase
{
    public function testProcess()
    {
        $mockHeader = m::mock(HeaderInterface::class);
        $mockHeader->expects('getFieldValue')->withNoArgs()->andReturn('COR_ID');

        $mockRequest = m::mock(Request::class);
        $mockRequest->expects('getHeader')->with('X-Correlation-Id')->andReturn($mockHeader);

        $sut = new CorrelationId($mockRequest);

        // run first time
        $data = $sut->process([]);
        $this->assertSame('COR_ID', $data['extra']['correlationId']);

        // run again to check cache property
        $data = $sut->process([]);
        $this->assertSame('COR_ID', $data['extra']['correlationId']);
    }

    public function testProcessCli()
    {
        $mockRequest = m::mock(RequestInterface::class);

        $sut = new CorrelationId($mockRequest);

        // run first time
        $data = $sut->process([]);
        $this->assertSame(null, $data['extra']['correlationId']);
    }
}
