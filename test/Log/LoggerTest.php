<?php

/**
 * Logger Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Logging\Log;

use Doctrine\ORM\Query\AST\ConditionalFactor;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use Laminas\Log\Logger as LaminasLogger;
use Laminas\Log\Writer\Mock;

/**
 * Logger Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LoggerTest extends MockeryTestCase
{
    private $logger;

    public function setUp(): void
    {
        $this->logger = m::mock(LaminasLogger::class);

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

    public function testLogResponseOk()
    {
        $this->logger->shouldReceive('log')->once()->with(7, 'foo', ['foo' => 'bar']);

        Logger::logResponse(200, 'foo', ['foo' => 'bar']);
    }

    public function testLogResponseClientError()
    {
        $this->logger->shouldReceive('log')->once()->with(6, 'foo', ['foo' => 'bar']);

        Logger::logResponse(400, 'foo', ['foo' => 'bar']);
    }

    public function testLogResponseServerError()
    {
        $this->logger->shouldReceive('log')->once()->with(3, 'foo', ['foo' => 'bar']);

        Logger::logResponse(500, 'foo', ['foo' => 'bar']);
    }

    public function testLogException()
    {
        $e = new \Exception('Foo', 200);
        $message = "Code 200 : Foo\n" . $e->getFile() . ' Line ' . $e->getLine();
        $this->logger->shouldReceive('log')->once()->with(7, $message, ['trace' => $e->getTraceAsString()]);

        Logger::logException($e);
    }
}
