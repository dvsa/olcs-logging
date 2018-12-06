<?php


namespace OlcsTest\Logging;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Module;

/**
 * Class ModuleTest
 * @package OlcsTest\Logging
 */
class ModuleTest extends TestCase
{
    public function testGetConfig()
    {
        $sut = new Module();
        $config = $sut->getConfig();

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('log', $config);
    }
}
 