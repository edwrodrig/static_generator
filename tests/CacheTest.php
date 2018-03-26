<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:31
 */

use edwrodrig\static_generator\cache\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{

    static public $log = [];

    static public function create_cache_item(string $key, DateTime $date) {
        $item = new class implements \edwrodrig\static_generator\cache\CacheItem {
            public $key;

            /**
             * @var DateTime
             */
            public $date;

            public function get_cache_key() : string {
                return $this->key;
            }

            public function get_last_modification_time() : DateTime {
                return $this->date;
            }

            public function cache_generate() {
                CacheTest::$log[] = "cache_generate_" . $this->key;
            }

            public function cache_remove() {
                CacheTest::$log[] = "cache_remove_" . $this->key;
            }

            public function jsonSerialize() {
                return [
                    'key' => $this->key,
                    'date' => $this->date->getTimestamp()
                ];
            }
        };

        $item->key = $key;
        $item->date = $date;

        return $item;
    }

    function setUp() {
        passthru('rm -rf /tmp/cache_test');
        self::$log = [];
    }

    function testCache() {

        $item = self::create_cache_item('hola', new DateTime('2000-01-01'));

        $cache = new Cache('/tmp/cache_test');

        $this->assertFalse($cache->is_hitted($item));
        $cache->update_cache($item);

        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola'], self::$log);

        $item = self::create_cache_item('hola', new DateTime('2000-01-01'));

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola'], self::$log);

        $item = self::create_cache_item('hola', new DateTime('3000-01-01'));

        $cache->update_cache($item);
        $this->assertTrue($cache->is_hitted($item));

        $this->assertEquals(['cache_generate_hola', 'cache_remove_hola', 'cache_generate_hola'], self::$log);

    }
}
