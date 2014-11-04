<?php


namespace OlcsTest\Logging\Log\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Log\Formatter\Exception;

/**
 * Class ExceptionTest
 * @package OlcsTest\Logging\Log\Formatter
 */
class ExceptionTest extends TestCase
{
    public function testFormat()
    {
        $event = [
            'timestamp' => new \DateTime('2014-10-10 15:30:22'),
            'microsecs' => '145234',
            'priority' => 3,
            'priorityName' => 'INFO',
            'extra' => [
                'userId' => '1',
                'sessionId' => 'adstdjkjht',
                'requestId' => 'sdkjhksdjh',
                'remoteIp' => '192.168.1.54',
                'exception' => new \Exception('error message', '33')
            ],
            'message' => 'hello world',

        ];

        $sut = new Exception();
        $string = $sut->format($event);

        $expected =
            '^^*2014-10-10 03:10:22.145234||3||INFO||||1||adstdjkjht||sdkjhksdjh|' .
            '|/home/valtech/git/olcs-logging/test/Log/Formatter/ExceptionTest.php:27|' .
            '|Exception||33||error message||{"remoteIp":"192.168.1.54"}||' . "\n" .
            '#0 [internal function]: OlcsTest\Logging\Log\Formatter\ExceptionTest->testFormat()';

        $this->assertStringStartsWith($expected, $string);
    }
}
