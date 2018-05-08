<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 07-05-18
 * Time: 21:10
 */

namespace test\edwrodrig\static_generator\util;

use edwrodrig\static_generator\util\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    /**
     * @var resource Store temp Logger output
     */
    private $target;


    public function setUp() {
        $this->target = fopen('php://memory', 'w+');
    }

    public function getTargetData() : string {
        rewind($this->target);
        return stream_get_contents($this->target);
    }

    public function tearDown() {
        fclose($this->target);
    }

    public function testBasic() {

        $logger = new Logger($this->target);
        $logger->log('Hola mundo');

        $this->assertEquals('Hola mundo', $this->getTargetData());
    }

    public function testCompletingDone() {

        $logger = new Logger($this->target);
        $logger
            ->begin('Completing...')
            ->end("DONE\n");

        $this->assertEquals("Completing...DONE\n", $this->getTargetData());
    }

    public function testCompletingDoneTwice() {

        $expected = <<<EOF
Completing...DONE
Completing...DONE

EOF;

        $logger = new Logger($this->target);
        $logger
            ->begin('Completing...')
            ->end("DONE\n")
            ->begin('Completing...')
            ->end("DONE\n");




        $this->assertEquals($expected, $this->getTargetData());
    }

    public function testCompletingDoneNested() {

        $logger = new Logger($this->target);
        $logger->begin('Completing...');
            $logger->begin('Nested...');
            $logger->end("DONE\n", false);
        $logger->end("DONE\n", false);


        $expected = <<<EOF
Completing...
  Nested...DONE
DONE

EOF;


        $this->assertEquals($expected, $this->getTargetData());
    }

    public function testCompletingDoneNested2() {

        $logger = new Logger($this->target);

        $logger
            ->begin('Completing...')
                ->begin('Nested...')
                ->end("DONE\n", false)
                ->begin('Nested...')
                ->end("DONE\n", false)
            ->end("DONE\n", false);


        $expected = <<<EOF
Completing...
  Nested...DONE
  Nested...DONE
DONE

EOF;


        $this->assertEquals($expected, $this->getTargetData());
    }

    public function testCompletingDoneNested3() {
        $expected = <<<EOF
Completing...
  Nested...
    Some Log
  DONE
  Nested...DONE
DONE

EOF;
        $logger = new Logger($this->target);

        $logger
            ->begin('Completing...')
                ->begin('Nested...')
                  ->log("Some Log\n")
                ->end("DONE\n", false)
                ->begin('Nested...')
                ->end("DONE\n", false)
            ->end("DONE\n", false);


        $this->assertEquals($expected, $this->getTargetData());
    }


    public function testCompletingDoneNested4() {
        $expected = <<<EOF
Completing...
  Nested...
    Some Log...OK
    Some final log
  DONE
  Nested...DONE
DONE

EOF;
        $logger = new Logger($this->target);

        $logger
            ->begin('Completing...')
              ->begin('Nested...')
                ->log("Some Log...")->log("OK\n", false)
                ->log("Some final log")
              ->end("DONE\n", false)
              ->begin('Nested...')
              ->end("DONE\n", false)
            ->end("DONE\n", false);


        $this->assertEquals($expected, $this->getTargetData());
    }


}
