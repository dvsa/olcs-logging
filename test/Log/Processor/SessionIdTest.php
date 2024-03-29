<?php

namespace OlcsTest\Logging\Log\Processor;

use Laminas\Session\ManagerInterface;
use Olcs\Logging\Log\Processor\SessionId;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\Session\Container;

/**
 * Class SessionIdTest
 * @package OlcsTest\Logging\Log\Processor
 */
class SessionIdTest extends TestCase
{
    public function testGetSessionManager()
    {
        $mockSessionManager = m::mock(ManagerInterface::class);
        Container::setDefaultManager($mockSessionManager);

        $sut = new SessionId();

        $manager = $sut->getSessionManager();
        $this->assertSame($mockSessionManager, $manager);

        Container::setDefaultManager(null);
    }

    public function testProcess()
    {
        $sessionId = 'ghastsdrf';

        $mockSessionManager = m::mock(ManagerInterface::class);
        $mockSessionManager->shouldReceive('start');
        $mockSessionManager->shouldReceive('getId')->andReturn($sessionId);

        $sut = new SessionId();
        $sut->setSessionManager($mockSessionManager);

        $data = $sut->process([]);

        $this->assertArrayHasKey('extra', $data);
        $this->assertArrayHasKey('sessionId', $data['extra']);

        $this->assertEquals($sessionId, $data['extra']['sessionId']);
    }
}
