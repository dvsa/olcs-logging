<?php

namespace OlcsTest\Logging\Log\Processor;

use Olcs\Logging\Log\Processor\RemoteIp;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class RemoteIpTest
 * @package OlcsTest\Logging\Log\Processor
 */
class RemoteIpTest extends TestCase
{
    public function testGetRemoteAddress()
    {
        $sut = new RemoteIp();
        $remoteAddr = $sut->getRemoteAddress();
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\RemoteAddress', $remoteAddr);
    }

    public function testProcess()
    {
        $ip = '192.168.1.54';

        $mockRemoteAddr = m::mock('Zend\Http\PhpEnvironment\RemoteAddress');
        $mockRemoteAddr->shouldReceive('getIpAddress')->andReturn($ip);

        $sut = new RemoteIp();
        $sut->setRemoteAddress($mockRemoteAddr);
        $data = $sut->process([]);

        $this->assertArrayHasKey('extra', $data);
        $this->assertArrayHasKey('remoteIp', $data['extra']);

        $this->assertEquals($ip, $data['extra']['remoteIp']);
    }
}
