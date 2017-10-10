<?php

namespace OlcsTest\Logging\Log;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Log\ZendLogPsr3Adapter;
use Psr\Log\LogLevel as LogLevel;
use Zend\Log\Logger as ZendLogger;

/**
 * Unit test for ZendLogPsr3Adapter
 *
 * @package OlcsTest\Logging\Log
 */
class ZendLogPsr3AdapterTest extends TestCase
{
    public function testProcess()
    {
        $level = LogLevel::EMERGENCY;
        $message = 'This is an error message';
        $context = (array)'This is some context';

        $logger = $this->createMock('\Zend\Log\Logger');
        $logger->expects($this->once())
            ->method('log')
            ->with(ZendLogger::EMERG, $message, $context)
            ->will($this->returnValue('SOME VALUE'));

        $sut = new ZendLogPsr3Adapter($logger);

        $this->assertEquals('SOME VALUE', $sut->log($level, $message, $context));
    }
}
