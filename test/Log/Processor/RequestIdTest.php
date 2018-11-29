<?php


namespace OlcsTest\Logging\Log\Processor;

use Olcs\Logging\Log\Processor\RequestId;

/**
 * Class RequestIdTest
 * @package OlcsTest\Logging\Log\Processor
 */
class RequestIdTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $sut = new RequestId();
        $data = $sut->process([]);
        $this->assertNotNull($data['extra']['requestId']);
    }
}
