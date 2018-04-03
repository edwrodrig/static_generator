<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:31
 */

use edwrodrig\static_generator\cache\Cache;
use edwrodrig\static_generator\cache\FileItem;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{

    static public $log = [];

    static public function create_cache_item(string $key, DateTime $date, string $salt) {
        $item = new class implements \edwrodrig\static_generator\cache\CacheItem {
            public $key;

            /**
             * @var DateTime
             */
            public $date;

            public $salt;

            public function get_cache_key() : string {
                return $this->key;
            }

            public function get_last_modification_time() : DateTime {
                return $this->date;
            }

            public function cache_generate(Cache $cache) {
                CacheTest::$log[] = "cache_generate_" . $this->get_cached_file();
                file_put_contents($cache->absolute_filename($this->get_cached_file()), 'hola');
            }

            public function get_cached_file() : string {
                return $this->key .'_' . $this->salt;
            }
        };

        $item->key = $key;
        $item->date = $date;
        $item->salt = $salt;

        return $item;
    }

    function setUp() {
        passthru('rm -rf /tmp/cache_test');
        self::$log = [];
    }

    function testCache() {

        $item = self::create_cache_item('hola', new DateTime('2000-01-01'), '123');

        $cache = new Cache('/tmp/cache_test');

        $this->assertFalse($cache->is_hitted($item));
        $cache->update_cache($item);

        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola_123'], self::$log);
        $this->assertFileExists($cache->absolute_filename('hola_123'));
        $this->assertFileNotExists($cache->absolute_filename('hola_345'));

        $item = self::create_cache_item('hola', new DateTime('2000-01-01'), '234');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola_123'], self::$log);
        $this->assertFileExists($cache->absolute_filename('hola_123'));
        $this->assertFileNotExists($cache->absolute_filename('hola_234'));

        $item = self::create_cache_item('hola', new DateTime('3000-01-01'), '345');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola_123', 'cache_generate_hola_345'], self::$log);
        $this->assertFileNotExists($cache->absolute_filename('hola_123'));
        $this->assertFileExists($cache->absolute_filename('hola_345'));

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

        $cache = new Cache('/tmp/cache_test');

        $this->assertFalse($cache->is_hitted($item));
        $cache->update_cache($item);

        $this->assertTrue($cache->is_hitted($item));

        $this->assertFileExists($cache->absolute_filename('hola1'));
        $this->assertEquals('A', file_get_contents($cache->absolute_filename('hola1')));

        $make_source('hola2', 'B');
        $item = $create_file('hola2');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertFileExists($cache->absolute_filename('hola1'));
        $this->assertFileExists($cache->absolute_filename('hola2'));
        $this->assertEquals('A', file_get_contents($cache->absolute_filename('hola1')));

        sleep(1);
        $make_source('hola1','C');
        $item = $create_file('hola1');

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertFileExists($cache->absolute_filename('hola1'));
        $this->assertFileExists($cache->absolute_filename('hola2'));
        $this->assertEquals('C', file_get_contents($cache->absolute_filename('hola1')));

    }
}
