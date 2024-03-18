<?php

namespace OlcsTest\Logging\Log\Formatter;

use Laminas\Log\Formatter\Base;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Formatter\Standard;

class StandardTest extends MockeryTestCase
{
    private $laminasBaseFormatter;
    private $sut;

    public function setUp(): void
    {
        $this->laminasBaseFormatter = m::mock(Base::class);
        $this->laminasBaseFormatter->expects('setDateTimeFormat')->with(\DateTimeInterface::W3C)->andReturnSelf();
        $this->sut = new Standard($this->laminasBaseFormatter);
    }

    public function testFormat(): void
    {
        $dateTime = new \DateTime('2015-02-18T15:30:22+01:00');

        $formattedTimestamp = clone($dateTime);
        $timeStampString = $formattedTimestamp->format(\DateTimeInterface::W3C);

        $event = [
            'timestamp' => $dateTime,
            'microsecs' => '145234',
            'priority' => 3,
            'priorityName' => 'INFO',
            'extra' => [
                'userId' => '1',
                'sessionId' => 'adstdjkjht',
                'requestId' => 'REQ_ID',
                'remoteIp' => '192.168.1.54',
                'data' => ['foo' => 'bar'],
                'correlationId' => 'COR_ID',
            ],
            'message' => 'hello world'
        ];

        $formattedEvent = [
            'timestamp' => $timeStampString,
            'microsecs' => '145234',
            'priority' => 3,
            'priorityName' => 'INFO',
            'extra' => [
                'userId' => '1',
                'sessionId' => 'adstdjkjht',
                'requestId' => 'REQ_ID',
                'remoteIp' => '192.168.1.54',
                'data' => ['foo' => 'bar'],
                'correlationId' => 'COR_ID',
            ],
            'message' => 'hello world'
        ];

        $this->laminasBaseFormatter->expects('format')->with($event)->andReturn($formattedEvent);

        $string = $this->sut->format($event);

        //all json encoded, plus userId, session_id and location deleted
        $expected = '{"timestamp":"2015-02-18 14:30:22.145234","log_priority":3,"log_priority_name":"INFO",' .
            '"log-entry-type":"","openam-uuid":"1","openam_session_token":"adstdjkjht","correlation_id":"COR_ID",' .
            '"location":"","relevant-message":"hello world","relevant-data":{"requestId":"REQ_ID",' .
            '"remoteIp":"192.168.1.54","data":{"foo":"bar"},"correlationId":"COR_ID"}}';

        $this->assertEquals($expected, $string);
    }

    public function testFormatWithException(): void
    {
        $dateTime = new \DateTime('2015-02-18T15:30:22+01:00');

        $formattedTimestamp = clone($dateTime);
        $timeStampString = $formattedTimestamp->format(\DateTimeInterface::W3C);

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
                'data' => ['foo' => 'bar'],
                'exception' => new \Exception('TEST EXCEPTION'),
                'correlationId' => 'COR_ID',
            ],
            'message' => 'hello world'
        ];

        $formattedEvent = [
            'timestamp' => $timeStampString,
            'microsecs' => '145234',
            'priority' => 3,
            'priorityName' => 'INFO',
            'extra' => [
                'userId' => '1',
                'sessionId' => 'adstdjkjht',
                'requestId' => 'REQ_ID',
                'remoteIp' => '192.168.1.54',
                'data' => ['foo' => 'bar'],
                'exception' => 'TEST EXCEPTION',
                'correlationId' => 'COR_ID',
            ],
            'message' => 'hello world'
        ];

        $this->laminasBaseFormatter->expects('format')->with($event)->andReturn($formattedEvent);

        $actual = $this->sut->format($event);
        $this->assertStringContainsString('TEST EXCEPTION', $actual);
    }
}
