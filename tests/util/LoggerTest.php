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


    public function setUp() : void {
        $this->target = fopen('php://memory', 'w+');
    }

    public function getTargetData() : string {
        rewind($this->target);
        return stream_get_contents($this->target);
    }

    public function tearDown() : void {
        fclose($this->target);
    }

    public function testBasic() {

        $logger = new Logger($this->target);
        $logger->log('Hola mundo');

        $this->assertEquals('Hola mundo', $this->getTargetData());
    }

    public function testCompletingDone() {

        $logger = new Logger($this->target);
        $logger->begin('Completing...');
        $logger->end("DONE", false);

        $this->assertEquals("Completing...DONE", $this->getTargetData());
    }

}
