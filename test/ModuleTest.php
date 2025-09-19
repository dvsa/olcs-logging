<?php

namespace OlcsTest\Logging;

use Laminas\Log\Logger as LaminasLogger;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Module;
use Mockery as m;

class ModuleTest extends MockeryTestCase
{
    public function testGetConfig()
    {
        $sut = new Module();
        $config = $sut->getConfig();

        $this->assertArrayHasKey('log', $config);
    }

    /**
     * @dataProvider dpTestOnBootstrap
     */
    public function testOnBootstrap($hideTimes, $logConfig)
    {
        $event = m::mock(\Laminas\EventManager\EventInterface::class);
        $logger = m::mock(LaminasLogger::class);

        $event->shouldReceive('getApplication->getServiceManager->get')->with('Logger')->once()->andReturn($logger);
        $event->shouldReceive('getApplication->getServiceManager->get')->with('Config')->once()->andReturn($logConfig);

        $logger->shouldReceive('addProcessor')
            ->times($hideTimes)
            ->andReturnSelf();

        $sut = new Module();
        $sut->onBootstrap($event);
    }

    public function dpTestOnBootstrap()
    {
        return [
            'noConfigEntry' => [1, []],
            'allowTrue' => [0, ['log' => ['allowPasswordLogging' => true]]],
            'allowFalse' => [1, 'log' => ['allowPasswordLogging' => false]],
            'allowAmbiguous' => [1, 'log' => ['allowPasswordLogging' => 'somestring']],
        ];
    }
}
