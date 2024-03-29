<?php

namespace OlcsTest\Logging\Log\Processor;

use Olcs\Logging\Log\Processor\UserId;

/**
 * Class UserIdTest
 * @package OlcsTest\Logging\Log\Processor
 */
class UserIdTest extends \PHPUnit\Framework\TestCase
{
    public function testProcessNoUser()
    {
        $sut = new UserId();
        $sut->setUserId(null);
        $data = $sut->process([]);

        $this->assertArrayHasKey('extra', $data);
        $this->assertArrayHasKey('userId', $data['extra']);

        $this->assertEquals(null, $data['extra']['userId']);
    }

    public function testProcessWithUserId()
    {
        $sut = new UserId();
        $sut->setUserId('USER123');
        $data = $sut->process([]);

        $this->assertArrayHasKey('extra', $data);
        $this->assertArrayHasKey('userId', $data['extra']);

        $this->assertEquals('USER123', $data['extra']['userId']);
    }
}
