<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 07-05-18
 * Time: 21:10
 */

namespace test\edwrodrig\static_generator\util;

use edwrodrig\static_generator\util\TemporaryLogger;
use PHPUnit\Framework\TestCase;

class TemporaryLoggerTest extends TestCase
{
    public function testBasic() {

        $logger = new TemporaryLogger();
        $logger->log('Hola mundo');

        $this->assertEquals('Hola mundo', $logger->getTargetData());
    }

    /**
     * @testWith    ["Completing...", "DONE", "Completing...DONE"]
     *              ["Completing...\n", "DONE\n", "Completing...DONE"]
     *              ["Completing...\nSomething...", "DONE" , "Completing...\nSomething...DONE"]
     * @param string $begin
     * @param string $end
     * @param string $expected
     */
    public function testBeginEnd(string $begin, string $end, string $expected) {

        $logger = new TemporaryLogger();
        $logger->begin($begin);
        $logger->end($end, false);

        $this->assertEquals($expected, $logger->getTargetData());
    }

    public function testCompletingDoneTwice() {

        $expected = <<<EOF
Completing...DONE
Completing...DONE
EOF;

        $logger = new TemporaryLogger();
        $logger
            ->begin('Completing...')->end("DONE\n", false)
            ->begin('Completing...')->end("DONE\n", false);




        $this->assertEquals($expected, $logger->getTargetData());
    }

    /**
     * @testWith    ["Nested...", "DONE", "Completing...\n  Nested...DONE\nDONE"]
     *              ["Nested...\nSome...", "DONE", "Completing...\n  Nested...\n  Some...DONE\nDONE"]
     * @param string $begin
     * @param string $end
     * @param string $expected
     */
    public function testCompletingDoneNested(string $begin, string $end, string $expected) {

        $logger = new TemporaryLogger();
        $logger->begin('Completing...');
            $logger->begin($begin);
            $logger->end($end, false);
        $logger->end("DONE\n", false);


        $this->assertEquals($expected, $logger->getTargetData());
    }

    public function testCompletingDoneNested2() {

        $logger = new TemporaryLogger();

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


        $this->assertEquals($expected, $logger->getTargetData());
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
        $logger = new TemporaryLogger();

        $logger
            ->begin('Completing...')
                ->begin('Nested...')
                  ->log("Some Log\n")
                ->end("DONE\n", false)
                ->begin('Nested...')
                ->end("DONE\n", false)
            ->end("DONE\n", false);


        $this->assertEquals($expected, $logger->getTargetData());
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
        $logger = new TemporaryLogger();

        $logger
            ->begin('Completing...')
              ->begin('Nested...')
                ->log("Some Log...")->log("OK\n", false)
                ->log("Some final log")
              ->end("DONE\n", false)
              ->begin('Nested...')
              ->end("DONE\n", false)
            ->end("DONE\n", false);


        $this->assertEquals($expected, $logger->getTargetData());


        $logger = new TemporaryLogger();

        $logger
            ->begin('Completing...')
            ->begin('Nested...')
            ->log("Some Log...OK\nSome final log")
            ->end("DONE\n", false)
            ->begin('Nested...')
            ->end("DONE\n", false)
            ->end("DONE\n", false);

        $this->assertEquals($expected, $logger->getTargetData());
    }


}
