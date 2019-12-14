<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-05-18
 * Time: 23:31
 */

namespace test\edwrodrig\static_generator\util;

use edwrodrig\static_generator\util\Util;
use Error;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

class UtilTest extends TestCase
{
    /**
     * @testWith    ["hola", "hola", []]
     *              ["hola1", "hola%s", ["1"]]
     *              [null, "hola%s", [null]]
     *              ["hola'", "hola%s", ["'"]]
     * @param $expected
     * @param $pattern
     * @param $args
     */

    public function testSprintfOrEmpty($expected, $pattern, $args)
    {
        $this->assertEquals($expected, Util::sprintfOrEmpty($pattern, ...$args));
    }

    public function testOutputBufferSafeHappyString()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $return = Util::outputBufferSafe('hola');
        $this->assertEquals('hola', $return);
    }

    public function testOutputBufferSafeHappyFunction()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $return = Util::outputBufferSafe(function() { echo 'hola';});
        $this->assertEquals('hola', $return);
    }


    public function testOutputBufferSafeException()
    {
        $return = null;
        try {
            $return = Util::outputBufferSafe(function () {
                echo 'hola';
                throw new Exception;
            });
        } catch ( Throwable $e ) {
            $this->assertInstanceOf(Exception::class, $e);
        }
        $this->assertEquals('', $return);

    }

    public function testOutputBufferSafeCaptureError()
    {
        $return = null;
        try {
            $return = Util::outputBufferSafe(function () {
                echo 'hola';
                eval('$hola = null; /** @noinspection PhpUndefinedMethodInspection */$hola->get();');
            });
        } catch ( Throwable $e ) {
            $this->assertInstanceOf(Error::class, $e);
        }
        $this->assertEquals('', $return);

    }
}
