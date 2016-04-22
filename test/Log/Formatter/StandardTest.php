<?php


namespace OlcsTest\Logging\Log\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Log\Formatter\Standard;

/**
 * Class StandardTest
 * @package OlcsTest\Logging\Log\Formatter
 */
class StandardTest extends TestCase
{
    public function testFormat()
    {
        $event = [
            'timestamp' => new \DateTime('2015-02-18T15:30:22+01:00'),
            'microsecs' => '145234',
            'priority' => 3,
            'priorityName' => 'INFO',
            'extra' => [
                'userId' => '1',
                'sessionId' => 'adstdjkjht',
                'requestId' => 'sdkjhksdjh',
                'remoteIp' => '192.168.1.54',
                'data' => [ 'foo' => 'bar' ],
            ],
            'message' => 'hello world'
        ];

        $sut = new Standard();
        $string = $sut->format($event);

        $expected =
            '^^*2015-02-18 14:30:22.145234||3||INFO||||1||adstdjkjht||sdkjhksdjh||' .
            '||hello world||{"remoteIp":"192.168.1.54","data":{"foo":"bar"}}';

        $this->assertEquals($expected, $string);
    }
}
