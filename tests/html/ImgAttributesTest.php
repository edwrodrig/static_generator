<?php
namespace test\edwrodrig\static_generator\html;

use edwrodrig\static_generator\html\ImgAttributes;
use PHPUnit\Framework\TestCase;

class ImgAttributesTest extends TestCase
{
    /**
     * @param string $expected
     * @param array $attributes
     * @testWith    ["src=\"http://edwin.cl\"", {"src": "http://edwin.cl"}]
     *              ["src=\"http://edwin.cl\" title=\"title of the link\" alt=\"link\" width=\"100\" height=\"34\"", {"src": "http://edwin.cl", "alt" : "link", "title": "title of the link", "width": 100, "height": 34}]
     */
    public function testCreate(string $expected, array $attributes)
    {
        $this->assertEquals($expected, strval(ImgAttributes::create($attributes)));
    }
}