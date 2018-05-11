<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 11-05-18
 * Time: 16:09
 */

namespace test\edwrodrig\static_generator\cache;

use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\cache\ImageItem;
use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class ImageItemTest extends TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }



    function testGetCachedFile() {
        $f = new ImageItem('http://edwin.cl', 'hola.jpg');
        $this->assertEquals('hola.jpg', $f->getTargetRelativePath());
    }

    function testHappy() {
        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager('cache', $context);

        $item = new ImageItem(__DIR__ . '/../files/image', 'rei.jpg');
        $item->resizeCover(100, 100);
        $this->assertEquals('rei_100x100_cover.jpg', $item->getTargetRelativePath());

        $manager->update($item);
        $manager->update($item);

        $this->assertFileExists($this->root->url() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $item->getTargetRelativePath());

        $item = new ImageItem(__DIR__ . '/../files/image', 'rei.jpg');
        $item->resizeContain(200, 100);
        $this->assertEquals('rei_200x100_contain.jpg', $item->getTargetRelativePath());

        $manager->update($item);
        $this->assertFileExists($this->root->url() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $item->getTargetRelativePath());

        $expected_log = <<<LOG
New cache entry [rei_100x100_cover]
  Generating cache file [rei_100x100_cover.jpg]...GENERATED
New cache entry [rei_200x100_contain]
  Generating cache file [rei_200x100_contain.jpg]...GENERATED

LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());
    }


}
