<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-05-18
 * Time: 23:06
 */

namespace test\edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\html\meta\SeoTags;
use PHPUnit\Framework\TestCase;

class SeoTagsTest extends TestCase
{

    public function testHappy() {
        $s = new SeoTags;
        $s->setDescription('hola');

        ob_start();
        $s->print();

        $output = ob_get_clean();
        $this->assertContains('<meta', $output);
        $this->assertContains('hola', $output);
    }

    public function testNull() {
        $s = new SeoTags;
        $s->setDescription(null);

        ob_start();
        $s->print();

        $output = ob_get_clean();
        $this->assertEmpty($output);
    }
}
