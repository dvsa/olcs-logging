<?php


namespace OlcsTest\Logging\Log\Processor;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Log\Processor\UserId;

/**
 * Class UserIdTest
 * @package OlcsTest\Logging\Log\Processor
 */
class UserIdTest extends TestCase
{
    public function testProcess()
    {
        $sut = new UserId();
        $data = $sut->process([]);

        $this->assertArrayHasKey('extra', $data);
        $this->assertArrayHasKey('userId', $data['extra']);

        $this->assertEquals('1', $data['extra']['userId']);
    }
}
 