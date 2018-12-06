<?php


namespace OlcsTest\Logging\Log\Processor;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Log\Processor\CorrelationId;
use Mockery as m;

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

        $mockRequest = m::mock(\Zend\Http\PhpEnvironment\Request::class)
            ->shouldReceive('getHeader')->with('X-Correlation-Id')->once()->andReturn($mockHeader)
            ->getMock();

        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator->get')->with('Request')->once()->andReturn($mockRequest)
            ->getMock();

        $sut = new CorrelationId();
        $sut->setServiceLocator($mockSl);

        // run first time
        $data = $sut->process([]);
        $this->assertSame('COR_ID', $data['extra']['correlationId']);

        // run again to check cache property
        $data = $sut->process([]);
        $this->assertSame('COR_ID', $data['extra']['correlationId']);
    }

    public function testProcessCli()
    {
        $mockRequest = m::mock(\Zend\Console\Request::class);

        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator->get')->with('Request')->once()->andReturn($mockRequest)
            ->getMock();

        $sut = new CorrelationId();
        $sut->setServiceLocator($mockSl);

        // run first time
        $data = $sut->process([]);
        $this->assertSame(null, $data['extra']['correlationId']);
    }
}
