<?php


namespace OlcsTest\Logging\Log\Formatter;

use Olcs\Logging\Log\Formatter\Exception;

/**
 * Class ExceptionTest
 * @package OlcsTest\Logging\Log\Formatter
 */
class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testFormat()
    {
        $exceptionMockName = 'Exception' . uniqid();
        $exception = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getFile', 'getLine', 'getCode', 'getMessage', 'getTraceAsString'])
            ->setMockClassName($exceptionMockName)
            ->getMock();
        $exception->expects($this->once())->method('getFile')->will($this->returnValue('File'));
        $exception->expects($this->once())->method('getLine')->will($this->returnValue('0'));
        $exception->expects($this->once())->method('getCode')->will($this->returnValue('Code'));
        $exception->expects($this->once())->method('getMessage')->will($this->returnValue('Message'));
        $exception->expects($this->once())->method('getTraceAsString')->will($this->returnValue('TraceAsString1'));

        $event = [
            'timestamp' => new \DateTime('2015-02-18T10:30:22+01:00'),
            'microsecs' => '145234',
            'priority' => 3,
            'priorityName' => 'INFO',
            'extra' => [
                'userId'    => '1',
                'sessionId' => 'adstdjkjht',
                'requestId' => 'sdkjhksdjh',
                'remoteIp'  => '192.168.1.54',
                'exception' => $exception
            ],
            'message' => 'hello world',

        ];

        $sut = new Exception();
        $string = $sut->format($event);

        $expected = '^^*2015-02-18 09:30:22.145234||3||INFO||||1||adstdjkjht||sdkjhksdjh||File:0||'
                  . $exceptionMockName . '||Code||Message||{"remoteIp":"192.168.1.54"}||' . "\nTraceAsString1";

        $this->assertEquals($expected, $string);
    }
}
