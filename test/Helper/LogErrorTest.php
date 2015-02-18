<?php


namespace OlcsTest\Logging\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Olcs\Logging\Helper\LogError;
use Zend\ServiceManager\Config;
use Mockery as m;

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

    public function testCreateService()
    {
        $mockLog = $this->getMockLog();

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($mockLog);

        $mockSl->shouldReceive('get')->with('Config')->andReturn(
            [
                'halt_on_error' => false,
            ]
        );

        $sut = new LogError();
        $service = $sut->createService($mockSl);

        $this->assertSame($sut, $service);
        $this->assertSame($mockLog, $service->getLogger());
    }
}
