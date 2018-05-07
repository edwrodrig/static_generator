<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 06-05-18
 * Time: 10:41
 */

namespace test\edwrodrig\static_generator\util;

use edwrodrig\static_generator\util\FileData;
use PHPUnit\Framework\TestCase;

class FileDataTest extends TestCase
{

    /**
     * @testWith ["edwrodrig\\static_generator\\PageCopy", "hola.jpg"]
     * @param string $expected_class
     * @param string $input_file
     */
    public function testGetGenerationClassName(string $expected_class, string $input_file) {
        $file_data = new FileData(0, $input_file, null);
        $this->assertEquals($expected_class, $file_data->getGenerationClassName());

    }

    public function testGetAbsolutePath() {
        $file_data = new FileData(0, 'hola.html', __DIR__ . '/../files/test_dir');
        $this->assertEquals(0, $file_data->getNestingLevel());
        $this->assertEquals('hola.html', $file_data->getRelativePath());
        $this->assertEquals(__DIR__ . '/../files/test_dir', $file_data->getRootPath());
        $this->assertEquals(__DIR__ . '/../files/test_dir/hola.html', $file_data->getAbsolutePath());
    }

    public function testGetIterator()
    {
        $file_data = new FileData(0, '.', __DIR__ . '/../files/test_dir');

        /**
         * @var $file_datas FileData[]
         */
        $file_datas = iterator_to_array($file_data, false);

        $this->assertCount(3, $file_datas);

        $current_file_data = $file_datas[0];
        $this->assertEquals(1, $current_file_data->getNestingLevel());
        $this->assertEquals('hola.html', $current_file_data->getRelativePath());
        $this->assertEquals(__DIR__ . '/../files/test_dir', $current_file_data->getRootPath());


        $current_file_data = $file_datas[1];
        $this->assertEquals(1, $current_file_data->getNestingLevel());
        $this->assertEquals('hola.php', $current_file_data->getRelativePath());
        $this->assertEquals(__DIR__ . '/../files/test_dir', $current_file_data->getRootPath());

        $current_file_data = $file_datas[2];
        $this->assertEquals(2, $current_file_data->getNestingLevel());
        $this->assertEquals('chao.html', $current_file_data->getRelativePath());
        $this->assertEquals(__DIR__ . '/../files/test_dir', $current_file_data->getRootPath());
    }
}
