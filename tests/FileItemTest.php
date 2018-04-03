<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-04-18
 * Time: 14:14
 */

use edwrodrig\static_generator\cache\FileItem;
use PHPUnit\Framework\TestCase;

class FileItemTest extends TestCase
{
    function getBasenameProvider() {
        return [
            ['b', 'b.exe'],
            ['hola/adf', 'hola/adf'],
            ['adf', 'adf'],
            ['hola/adf', 'hola/adf.exe'],
            ['http://edwin.cl/hola', 'http://edwin.cl/hola.jpg'],
            ['http://edwin.cl/hola', 'http://edwin.cl/hola']
        ];
    }

    /**
     * @dataProvider getBasenameProvider
     * @param string $expected
     * @param string $filename
     */
    function testGetBasename(string $expected, string $filename) {
        $this->assertEquals($expected, FileItem::get_basename($filename));
    }
}
