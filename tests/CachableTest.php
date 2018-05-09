<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:31
 */

namespace test\edwrodrig\static_generator;

use DateTime;
use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\cache\FileItem;
use PHPUnit\Framework\TestCase;

/**
 * Class CacheTest
 * @package test\edwrodrig\static_generator
 * @ig
 */
class CacheTest extends TestCase
{

    static public $log = [];


    static public function create_cache_item(string $key, DateTime $date, string $salt) {
        $item = new class implements \edwrodrig\static_generator\cache\CacheableItem {
            public $key;

            /**
             * @var DateTime
             */
            public $date;

            public $salt;

            public function getKey() : string {
                return $this->key;
            }

            public function getLastModificationTime() : DateTime {
                return $this->date;
            }

            public function generate(CacheManager $cache) {
                CacheTest::$log[] = "cache_generate_" . $this->get_cached_file();
                file_put_contents($cache->cache_filename($this->get_cached_file()), 'hola');
            }

            public function get_output_filename() : string {
                return $this->get_cached_file();
            }
            public function getTargetRelativePath() : string {
                return $this->key .'_' . $this->salt;
            }
        };

        $item->key = $key;
        $item->date = $date;
        $item->salt = $salt;

        return $item;
    }

    function setUp() {
        $this->markTestIncomplete();
        passthru('rm -rf /tmp/cache_test');
        self::$log = [];
    }

    function testCache() {

        $item = self::create_cache_item('hola', new DateTime('2000-01-01'), '123');

        $cache = new CacheManager('/tmp/cache_test');

        $this->assertFalse($cache->is_hitted($item));
        $cache->update_cache($item);

        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola_123'], self::$log);
        $this->assertFileExists($cache->cache_filename('hola_123'));
        $this->assertFileNotExists($cache->cache_filename('hola_345'));

        $item = self::create_cache_item('hola', new DateTime('2000-01-01'), '234');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola_123'], self::$log);
        $this->assertFileExists($cache->cache_filename('hola_123'));
        $this->assertFileNotExists($cache->cache_filename('hola_234'));

        $item = self::create_cache_item('hola', new DateTime('3000-01-01'), '345');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola_123', 'cache_generate_hola_345'], self::$log);
        $this->assertFileNotExists($cache->cache_filename('hola_123'));
        $this->assertFileExists($cache->cache_filename('hola_345'));

        $cache->save_index();

        $index_filename = $cache->get_index_filename();
        $this->assertFileExists($index_filename);
        $data = json_decode(file_get_contents($index_filename), true);
        $this->assertArraySubset(
            [
                'hola' => [
                    'cache_key' => 'hola',
                    'output_filename' => 'hola_345',
                    'cached_file' => 'hola_345'
                ]
            ],
            $data);


    }

    function testCacheFile() {
        @passthru('rm -rf /tmp/cache_source');
        @mkdir('/tmp/cache_source', 0777, true);

        $make_source = function($filename, $content) {
            file_put_contents('/tmp/cache_source/' . $filename, $content);
        };

        $create_file = function($filename) {
            return new FileItem('/tmp/cache_source', $filename);
        };

        $make_source('hola1', 'A');
        $item = $create_file('hola1');

        $cache = new CacheManager('/tmp/cache_test');

        $this->assertFalse($cache->is_hitted($item));
        $cache->update_cache($item);

        $this->assertTrue($cache->is_hitted($item));

        $this->assertFileExists($cache->cache_filename('hola1'));
        $this->assertEquals('A', file_get_contents($cache->cache_filename('hola1')));

        $make_source('hola2', 'B');
        $item = $create_file('hola2');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertFileExists($cache->cache_filename('hola1'));
        $this->assertFileExists($cache->cache_filename('hola2'));
        $this->assertEquals('A', file_get_contents($cache->cache_filename('hola1')));

        sleep(1);
        $make_source('hola1','C');
        $item = $create_file('hola1');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertFileExists($cache->cache_filename('hola1'));
        $this->assertFileExists($cache->cache_filename('hola2'));
        $this->assertEquals('C', file_get_contents($cache->cache_filename('hola1')));

    }
}
