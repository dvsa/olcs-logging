<?php


namespace OlcsTest\Logging\Log\Processor;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Log\Processor\RequestId;

/**
 * Class RequestIdTest
 * @package OlcsTest\Logging\Log\Processor
 */
class RequestIdTest extends TestCase
{
    public function testProcess()
    {
        $sut = new RequestId();
        $data = $sut->process([]);
        $this->assertNotNull($data['extra']['requestId']);
    }
}
