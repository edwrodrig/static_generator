<?php

namespace test\edwrodrig\static_generator\cache;


use DateTime;
use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class CacheManagerTest extends TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }



    public function testManager() {

        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $item = new CacheableItem('abc', new DateTime('2015-01-01'), 'salt');
        $manager->update($item);

        $manager->update($item);

        $item = new CacheableItem('abc', new DateTime('2016-01-01'), 'salt');

        $manager->update($item);

        $expected_log = <<<LOG
New cache entry [abc]
  Generating cache file [abc_salt]...GENERATED
Outdated cache entry [abc] FOUND!
  Removing file [abc_salt]...REMOVED
  Generating cache file [abc_salt]...GENERATED
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());
    }


    public function testManagerSaveAndRestored() {
        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $item = new CacheableItem('abc', new DateTime('2015-01-01'), 'salt');
        $manager->update($item);

        $expected_log = <<<LOG
New cache entry [abc]
  Generating cache file [abc_salt]...GENERATED
LOG;
        $this->assertEquals($expected_log, $logger->getTargetData());

        $manager->save();

        $logger = new TemporaryLogger;
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $manager->update($item);

        $item = new CacheableItem('abc', new DateTime('2016-01-01'), 'salt');

        $manager->update($item);

        $expected_log = <<<LOG
Outdated cache entry [abc] FOUND!
  Removing file [abc_salt]...REMOVED
  Generating cache file [abc_salt]...GENERATED
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());


    }


    public function testManagerUnused() {
        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $item_1 = new CacheableItem('abc', new DateTime('2015-01-01'), 'salt');
        $item_2 = new CacheableItem('zxc', new DateTime('2016-01-01'), 'salt');

        $manager->update($item_1);
        $manager->update($item_2);

        $manager->save();

        $expected_log = <<<LOG
New cache entry [abc]
  Generating cache file [abc_salt]...GENERATED
New cache entry [zxc]
  Generating cache file [zxc_salt]...GENERATED
LOG;
        $this->assertEquals($expected_log, $logger->getTargetData());


        $logger = new TemporaryLogger;
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $manager->update($item_1);
        $manager->save();

        $expected_log = <<<LOG
Unused cache entry [zxc] FOUND!
  Removing file [zxc_salt]...REMOVED
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());

        $logger = new TemporaryLogger;
        $context->setLogger($logger);

        $manager = new CacheManager( $this->root->url() . '/cache');
        $manager->setContext($context);

        $manager->update($item_1);
        $manager->update($item_2);
        $manager->save();

        $expected_log = <<<LOG
New cache entry [zxc]
  Generating cache file [zxc_salt]...GENERATED
LOG;

        $this->assertEquals($expected_log, $logger->getTargetData());


    }

}