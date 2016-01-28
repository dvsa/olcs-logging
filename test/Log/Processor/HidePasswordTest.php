<?php


namespace OlcsTest\Logging\Log\Processor;

use Olcs\Logging\Log\Processor\HidePassword;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class HidePasswordTest
 * @package OlcsTest\Logging\Log\Processor
 */
class HidePasswordTest extends TestCase
{
    public function testProcess()
    {
        $event = [
            'foo' => 'bar',
            'some' => 'asdaspaSSworddasd',
            'some' => 'thing',
            'password' => 'another-password',
            'something' => [
                'somethingelse' => [
                    'foo' => 'bar',
                    'passWORD' => 'secret',
                    'content' => 'asdaspassworddasd',
                    'foo2' => 'bar2',
                ],
            ],
            'foo2' => 'bar2',
        ];

        $sut = new HidePassword();

        $this->assertSame(
            [
                'foo' => 'bar',
                'some' => '*** HIDDEN PASSWORD ***',
                'some' => 'thing',
                'password' => '*** HIDDEN PASSWORD ***',
                'something' => [
                    'somethingelse' => [
                        'foo' => 'bar',
                        'passWORD' => '*** HIDDEN PASSWORD ***',
                        'content' => '*** HIDDEN PASSWORD ***',
                        'foo2' => 'bar2',
                    ],
                ],
                'foo2' => 'bar2',
            ],
            $sut->process($event)
        );
    }
}
