<?php

namespace OlcsTest\Logging\Log;

use Olcs\Logging\Log\LaminasLogPsr3Adapter;
use Psr\Log\LogLevel as LogLevel;
use Laminas\Log\Logger as LaminasLogger;

/**
 * Unit test for LaminasLogPsr3Adapter
 *
 * @package OlcsTest\Logging\Log
 */
class LaminasLogPsr3AdapterTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $level = LogLevel::EMERGENCY;
        $message = 'This is an error message';
        $context = (array)'This is some context';

        $logger = $this->createMock(LaminasLogger::class);
        $logger->expects($this->once())
            ->method('log')
            ->with(LaminasLogger::EMERG, $message, $context)
            ->will($this->returnSelf());

        $sut = new LaminasLogPsr3Adapter($logger);

        $this->assertEquals($logger, $sut->log($level, $message, $context));
    }
}
