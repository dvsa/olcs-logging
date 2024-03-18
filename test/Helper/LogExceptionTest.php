<?php

namespace OlcsTest\Logging\Helper;

use Laminas\Log\Logger;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Olcs\Logging\Helper\LogException;
use Mockery as m;

/**
 * Class LogExceptionTest
 * @package OlcsTest\Logging\Helper
 */
class LogExceptionTest extends TestCase
{
    public function testLogException()
    {
        $e3 = new \Exception('3rd error');
        $e2 = new \Exception('nested error', 22, $e3);
        $e1 = new \Exception('error', 11, $e2);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->ordered('logcalls')->with('', ['exception' => $e3]);
        $mockLog->shouldReceive('info')->ordered('logcalls')->with('', ['exception' => $e2]);
        $mockLog->shouldReceive('err')->atLeast()->once()->ordered('logcalls')->with('Exception : error', ['exception' => $e1]);

        $sut = new LogException();
        $sut->setLogger($mockLog);
        $sut->logException($e1);
    }

    public function testInvoke(): void
    {
        $mockLog = $this->getMockLog();

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($mockLog);

        $sut = new LogException();

        $service = $sut->__invoke($mockSl, LogException::class);
        $this->assertSame($sut, $service);
        $this->assertSame($mockLog, $service->getLogger());
    }

    protected function getMockLog()
    {
        return m::mock(Logger::class);
    }
}
