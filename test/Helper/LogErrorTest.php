<?php

namespace OlcsTest\Logging\Helper;

use Psr\Container\ContainerInterface;
use Olcs\Logging\Helper\LogError;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class LogErrorTest
 * @package OlcsTest\Logging\Helper
 */
class LogErrorTest extends TestCase
{
    /**
     * @return m\MockInterface
     */
    protected function getMockLog()
    {
        $mockConfig = new m\Generator\MockConfigurationBuilder();
        $mockConfig->setBlackListedMethods(
            [
                '__call',
                '__callStatic',
                '__clone',
                '__wakeup',
                '__set',
                '__get',
                '__toString',
                '__isset',
                '__destruct',

                // below are reserved words in PHP
                "__halt_compiler", "abstract", "and", "array", "as",
                "break", "callable", "case", "catch", "class",
                "clone", "const", "continue", "declare", "default",
                "die", "do", "echo", "else", "elseif",
                "empty", "enddeclare", "endfor", "endforeach", "endif",
                "endswitch", "endwhile", "eval", "exit", "extends",
                "final", "for", "foreach", "function", "global",
                "goto", "if", "implements", "include", "include_once",
                "instanceof", "insteadof", "interface", "isset", "list",
                "namespace", "new", "or", "print", "private",
                "protected", "public", "require", "require_once", "return",
                "static", "switch", "throw", "trait", "try",
                "unset", "use", "var", "while", "xor"
            ]
        );
        $mockConfig->addTarget('Laminas\Log\Logger');

        $mockLog = m::mock($mockConfig);
        $mockLog->shouldReceive('__destruct');
        return $mockLog;
    }

    public function testInvoke(): void
    {
        $mockLog = $this->getMockLog();

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($mockLog);

        $mockSl->shouldReceive('get')->with('Config')->andReturn(
            [
                'halt_on_error' => false,
            ]
        );

        $sut = new LogError();
        $service = $sut->__invoke($mockSl, LogError::class);

        $this->assertSame($sut, $service);
        $this->assertSame($mockLog, $service->getLogger());
    }
}
