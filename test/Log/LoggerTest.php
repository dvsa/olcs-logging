<?php

/**
 * Logger Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Logging\Log;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use Zend\Log\Logger as ZendLogger;
use Zend\Log\Writer\Mock;

/**
 * Logger Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LoggerTest extends MockeryTestCase
{
    private $logger;

    public function setUp()
    {
        $writer = new Mock();
        $this->logger = m::mock(ZendLogger::class, [null])->makePartial();

        $this->logger->addWriter($writer);

        Logger::setLogger($this->logger);

        $this->assertSame($this->logger, Logger::getLogger());
    }

    public function testEmerg()
    {
        $this->logger->shouldReceive('emerg')->once()->with('foo', ['foo' => 'bar']);

        Logger::emerg('foo', ['foo' => 'bar']);
    }

    public function testAlter()
    {
        $this->logger->shouldReceive('alert')->once()->with('foo', ['foo' => 'bar']);

        Logger::alert('foo', ['foo' => 'bar']);
    }

    public function testCrit()
    {
        $this->logger->shouldReceive('crit')->once()->with('foo', ['foo' => 'bar']);

        Logger::crit('foo', ['foo' => 'bar']);
    }

    public function testErr()
    {
        $this->logger->shouldReceive('err')->once()->with('foo', ['foo' => 'bar']);

        Logger::err('foo', ['foo' => 'bar']);
    }

    public function testWarn()
    {
        $this->logger->shouldReceive('warn')->once()->with('foo', ['foo' => 'bar']);

        Logger::warn('foo', ['foo' => 'bar']);
    }

    public function testNotice()
    {
        $this->logger->shouldReceive('notice')->once()->with('foo', ['foo' => 'bar']);

        Logger::notice('foo', ['foo' => 'bar']);
    }

    public function testInfo()
    {
        $this->logger->shouldReceive('info')->once()->with('foo', ['foo' => 'bar']);

        Logger::info('foo', ['foo' => 'bar']);
    }

    public function testDebug()
    {
        $this->logger->shouldReceive('debug')->once()->with('foo', ['foo' => 'bar']);

        Logger::debug('foo', ['foo' => 'bar']);
    }

    public function testLog()
    {
        $this->logger->shouldReceive('log')->once()->with(1, 'foo', ['foo' => 'bar']);

        Logger::log(1, 'foo', ['foo' => 'bar']);
    }
}
