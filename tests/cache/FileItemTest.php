<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-04-18
 * Time: 14:14
 */

namespace test\edwrodrig\static_generator\cache;

use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\cache\FileItem;
use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\TemporaryLogger;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class FileItemTest extends TestCase
{

    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }


    /**
     * @param string $expected
     * @param string $filename
     * @testWith    ["b", "b.exe"]
     *              ["hola/adf", "hola/adf"]
     *              ["adf", "adf"]
     *              ["hola/adf", "hola/adf.exe"]
     *              ["http://edwin.cl/hola", "http://edwin.cl/hola.jpg"]
     *              ["http://edwin.cl/hola", "http://edwin.cl/hola"]
     */
    function testGetBasename(string $expected, string $filename) {
        $this->assertEquals($expected, FileItem::getBasename($filename));
    }

    function testGetCachedFile() {
        $f = new FileItem('http://edwin.cl', 'hola.jpg');
        $f->setVersion('rojo');
        $this->assertEquals('hola_rojo.jpg', $f->getTargetRelativePath());
    }

    /**
     * @throws Exception
     */
    function testHappy() {
        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $item = new FileItem(__DIR__, 'FileItemTest.php');
        $item->setVersion('rojo');
        $this->assertEquals('FileItemTest_rojo.php', $item->getTargetRelativePath());
        $this->assertEquals([], $item->getAdditionalData());

        $manager->update($item);
        $manager->update($item);

        $this->assertFileExists($this->root->url() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $item->getTargetRelativePath());

        $expected_log = <<<LOG
New cache entry [FileItemTest_rojo]
  Generating cache file [FileItemTest_rojo.php]...GENERATED
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());
    }

    /**
     * @throws Exception
     */
    function testExtensionOverride() {
        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $item = new FileItem(__DIR__, 'FileItemTest.php');
        $item->setVersion('rojo');
        $item->setTargetExtension('cpp');
        $this->assertEquals('FileItemTest_rojo.cpp', $item->getTargetRelativePath());

        $manager->update($item);
        $manager->update($item);

        $this->assertFileExists($this->root->url() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $item->getTargetRelativePath());

        $expected_log = <<<LOG
New cache entry [FileItemTest_rojo]
  Generating cache file [FileItemTest_rojo.cpp]...GENERATED
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());
    }

    /**
     * @throws Exception
     */
    function testSalted() {
        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $item = new FileItem(__DIR__, 'FileItemTest.php');
            $item
                ->setVersion('rojo')
                ->setSalt();

        $manager->update($item);
        $manager->update($item);

        $target_relative_path = $item->getTargetRelativePath();
        $this->assertFileExists($this->root->url() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $target_relative_path);


        $expected_log = <<<LOG
New cache entry [FileItemTest_rojo]
  Generating cache file [$target_relative_path]...GENERATED
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());
    }

}
