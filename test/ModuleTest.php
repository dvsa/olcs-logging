<?php


namespace OlcsTest\Logging;

use Olcs\Logging\Module;

/**
 * Class ModuleTest
 * @package OlcsTest\Logging
 */
class ModuleTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfig()
    {
        $sut = new Module();
        $config = $sut->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('log', $config);
    }
}
