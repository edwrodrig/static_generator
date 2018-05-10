<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:31
 */

namespace test\edwrodrig\static_generator\cache;

use DateTime;
use org\bovigo\vfs\vfsStream;
use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\TemporaryLogger;
use PHPUnit\Framework\TestCase;

/**
 * Class CacheTest
 * @package test\edwrodrig\static_generator
 * @ig
 */
class CacheEntryTest extends TestCase
{
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }


    function testCacheEntryUpdateSameEntry() {

        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
            $context->setLogger($logger);

        $manager = new CacheManager('out', $context);
        $item = new CacheableItem('abc', new DateTime('2015-01-01'), 'salt');


        $entry = $manager->update($item);

        $this->assertEquals('abc', $entry->getKey());
        $this->assertEquals('abc_salt', $entry->getTargetRelativePath());

        $item = new CacheableItem('abc', new DateTime('2015-01-01'), 'salt_2');
        $entry = $manager->update($item);

        $this->assertEquals('abc', $entry->getKey());
        $this->assertEquals('abc_salt', $entry->getTargetRelativePath());



        $expected_log = <<<LOG
New cache entry [abc]
  Generating cache file [abc_salt]...GENERATED

LOG;
        $this->assertEquals($expected_log, $logger->getTargetData());

    }


    function testCacheEntryUpdateRemovedCachedFile() {

        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager('out', $context);
        $item = new CacheableItem('abc', new DateTime('2015-01-01'), 'salt');


        $entry = $manager->update($item);

        $this->assertEquals('abc', $entry->getKey());
        $this->assertEquals('abc_salt', $entry->getTargetRelativePath());

        $entry->removeCachedFile();

        $item = new CacheableItem('abc', new DateTime('2014-01-01'), 'salt_2');
        $entry = $manager->update($item);

        $this->assertEquals('abc', $entry->getKey());
        $this->assertEquals('abc_salt_2', $entry->getTargetRelativePath());


        $item = new CacheableItem('abc', new DateTime('2014-02-01'), 'salt_3');
        $entry = $manager->update($item);

        $this->assertEquals('abc', $entry->getKey());
        $this->assertEquals('abc_salt_3', $entry->getTargetRelativePath());


        $entry = $manager->update($item);

        $expected_log = <<<LOG
New cache entry [abc]
  Generating cache file [abc_salt]...GENERATED
Removing file [abc_salt]...REMOVED
Cache file [abc_salt] NOT FOUND!
  Generating cache file [abc_salt_2]...GENERATED
Outdated cache entry [abc] FOUND!
  Removing file [abc_salt_2]...REMOVED
  Generating cache file [abc_salt_3]...GENERATED

LOG;
        $this->assertEquals($expected_log, $logger->getTargetData());
    }


    function testCacheEntryUpdateModifiedFile() {

        $logger = new TemporaryLogger;
        $context = new Context(__DIR__ . '/../files/test_dir', $this->root->url());
        $context->setLogger($logger);

        $manager = new CacheManager('out', $context);
        $item = new CacheableItem('abc', new DateTime('2015-01-01'), 'salt');


        $entry = $manager->update($item);

        $this->assertEquals('abc', $entry->getKey());
        $this->assertEquals('abc_salt', $entry->getTargetRelativePath());

        $item = new CacheableItem('abc', new DateTime('2016-01-01'), 'salt_2');
        $entry = $manager->update($item);

        $this->assertEquals('abc', $entry->getKey());
        $this->assertEquals('abc_salt_2', $entry->getTargetRelativePath());

    $expected_log = <<<LOG
New cache entry [abc]
  Generating cache file [abc_salt]...GENERATED
Outdated cache entry [abc] FOUND!
  Removing file [abc_salt]...REMOVED
  Generating cache file [abc_salt_2]...GENERATED

LOG;
        $this->assertEquals($expected_log, $logger->getTargetData());
    }
}

