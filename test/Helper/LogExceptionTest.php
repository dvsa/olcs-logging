<?php

namespace OlcsTest\Logging\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Olcs\Logging\Helper\LogException;
use Mockery as m;

/**
 * Class LogExceptionTest
 * @package OlcsTest\Logging\Helper
 */
class LogExceptionTest extends TestCase
{
    public function testLogException()
    {
        $e3 = new \Exception('3rd error');
        $e2 = new \Exception('nested error', 22, $e3);
        $e1 = new \Exception('error', 11, $e2);

        $mockLog = $this->getMockLog();
        $mockLog->shouldReceive('info')->ordered('logcalls')->with('', ['exception' => $e3]);
        $mockLog->shouldReceive('info')->ordered('logcalls')->with('', ['exception' => $e2]);
        $mockLog->shouldReceive('err')->ordered('logcalls')->with('', ['exception' => $e1]);

        $sut = new LogException();
        $sut->setLogger($mockLog);
        $sut->logException($e1);
    }

    public function testCreateService()
    {
        $mockLog = $this->getMockLog();

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($mockLog);

        $sut = new LogException();

        $service = $sut->createService($mockSl);
        $this->assertSame($sut, $service);
        $this->assertSame($mockLog, $service->getLogger());
    }

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
        $mockConfig->addTarget('Zend\Log\Logger');

        $mockLog = m::mock($mockConfig);
        $mockLog->shouldReceive('__destruct');
        return $mockLog;
    }
}
