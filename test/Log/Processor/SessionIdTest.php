<?php


namespace OlcsTest\Logging\Log\Processor;

use Olcs\Logging\Log\Processor\SessionId;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Zend\Session\Container;

/**
 * Class SessionIdTest
 * @package OlcsTest\Logging\Log\Processor
 */
class SessionIdTest extends TestCase
{
    public function testGetSessionManager()
    {
        $mockSessionManager = m::mock('Zend\Session\ManagerInterface');
        Container::setDefaultManager($mockSessionManager);

        $sut = new SessionId();

        $manager = $sut->getSessionManager();
        $this->assertSame($mockSessionManager, $manager);

        Container::setDefaultManager(null);
    }

    public function testProcess()
    {
        $sessionId = 'ghastsdrf';

        $mockSessionManager = m::mock('Zend\Session\ManagerInterface');
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
