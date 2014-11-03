<?php


namespace OlcsTest\Logging\Log\Processor;

use Olcs\Logging\Log\Processor\Microtime;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class MicrotimeTest
 * @package OlcsTest\Logging\Log\Processor
 */
class MicrotimeTest extends TestCase
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
 