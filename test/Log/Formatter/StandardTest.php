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
                'requestId' => 'REQ_ID',
                'remoteIp' => '192.168.1.54',
                'data' => [ 'foo' => 'bar' ],
                'correlationId' => 'COR_ID',
            ],
            'message' => 'hello world'
        ];

        $sut = new Standard();
        $string = $sut->format($event);

        $expected = '{"timestamp":"2015-02-18 14:30:22.145234","log_priority":3,"log_priority_name":"INFO",'.
            '"log-entry-type":"","openam-uuid":"1","openam_session_token":"adstdjkjht","correlation_id":"COR_ID",'.
            '"location":"","relevant-message":"hello world","relevant-data":{"requestId":"REQ_ID",'.
            '"remoteIp":"192.168.1.54","data":"{\"foo\":\"bar\"}","correlationId":"COR_ID"}}';

        $this->assertEquals($expected, $string);
    }

    public function testFormatWithException()
    {
        $event = [
            'timestamp' => new \DateTime('2015-02-18T15:30:22+01:00'),
            'microsecs' => '145234',
            'priority' => 3,
            'priorityName' => 'INFO',
            'extra' => [
                'userId' => '1',
                'sessionId' => 'adstdjkjht',
                'requestId' => 'REQ_ID',
                'remoteIp' => '192.168.1.54',
                'data' => [ 'foo' => 'bar' ],
                'exception' => new \Exception('TEST EXCEPTION'),
                'correlationId' => 'COR_ID',
            ],
            'message' => 'hello world'
        ];

        $sut = new Standard();
        $string = $sut->format($event);
        $this->assertContains('\'Exception\' with message \'TEST EXCEPTION\' in', $string);
    }
}
