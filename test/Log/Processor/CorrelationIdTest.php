<?php

namespace OlcsTest\Logging\Log\Processor;

use Laminas\Stdlib\RequestInterface;
use Olcs\Logging\Log\Processor\CorrelationId;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class CorrelationIdTest
 * @package OlcsTest\Logging\Log\Processor
 */
class CorrelationIdTest extends TestCase
{
    public function testProcess()
    {
        $mockHeader = m::mock()
            ->shouldReceive('getFieldValue')->with()->once()->andReturn('COR_ID')
            ->getMock();

        $mockRequest = m::mock(\Laminas\Http\PhpEnvironment\Request::class)
            ->shouldReceive('getHeader')->with('X-Correlation-Id')->once()->andReturn($mockHeader)
            ->getMock();

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
