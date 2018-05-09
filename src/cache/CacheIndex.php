<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 08-05-18
 * Time: 23:42
 */

namespace edwrodrig\static_generator\cache;


class CacheIndex
{
    private $data = [];

    /**
     * The base path of this cache index
     * @var string
     */
    private $path;

    /**
     * CacheIndex constructor.
     * Creates a cache index object
     * @param string $filename
     */
    public function __construct(string $filename) {

        $this->path = dirname($filename);
        if ( !file_exists($filename) ) return;

        $index_data = file_get_contents($filename);
        if ( $index_data = json_decode($index_data, true) ) {
            foreach ( $index_data as $cache_key => $entry_data ) {
                $this->data[$cache_key] = CacheEntry::create_from_array($entry_data);
            }
        }
    }
}