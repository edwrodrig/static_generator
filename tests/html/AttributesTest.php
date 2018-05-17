<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 17-05-18
 * Time: 6:18
 */

namespace test\edwrodrig\static_generator\html;

use edwrodrig\static_generator\html\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{


    /**
     * @param string $expected
     * @param array $attributes
     * @testWith    ["", []]
     *              ["", {"hola": null}]
     *              ["", {"hola": false}]
     *              ["hola", {"hola": true}]
     *              ["hola=\"chao\"", {"hola": "chao"}]
     *              ["hola=\"chao\"", {"hola": "chao"}]
     *              ["a=\"aa\" b=\"bb\"", {"a": "aa", "b": "bb"}]
     *              ["a=\"aa\" b=\"bb\"", {"a": "aa", "invalid": null, "b": "bb"}]
     */
    public function testCreate(string $expected, array $attributes)
    {
        $this->assertEquals($expected, strval(Attributes::create($attributes)));
    }
}
