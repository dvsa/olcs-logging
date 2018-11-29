<?php


namespace OlcsTest\Logging\Log\Processor;

use Olcs\Logging\Log\Processor\Microtime;

/**
 * Class MicrotimeTest
 * @package OlcsTest\Logging\Log\Processor
 */
class MicrotimeTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $sut = new Microtime();
        $data = $sut->process([]);

        $this->assertArrayHasKey('microsecs', $data);
        $this->assertEquals(6, strlen($data['microsecs']));
        $this->assertTrue(is_numeric($data['microsecs']), 'Microsecs wasn\'t an number');
    }
}
