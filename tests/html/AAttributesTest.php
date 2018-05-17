<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 17-05-18
 * Time: 7:21
 */

namespace test\edwrodrig\static_generator\html;

use edwrodrig\static_generator\html\AAttributes;
use PHPUnit\Framework\TestCase;

class AAttributesTest extends TestCase
{
    /**
     * @param string $expected
     * @param array $attributes
     * @testWith    ["href=\"http://edwin.cl\"", {"href": "http://edwin.cl"}]
     *              ["download", {"download": true}]
     *              ["download=\"some\"", {"download": "some"}]
     *              ["href=\"http://edwin.cl\" title=\"title of the link\" rel=\"link\" download target=\"_blank\"", {"href": "http://edwin.cl", "rel" : "link", "title": "title of the link", "target": "_blank", "download": true}]
     */
    public function testCreate(string $expected, array $attributes)
    {
        $this->assertEquals($expected, strval(AAttributes::create($attributes)));
    }
}
