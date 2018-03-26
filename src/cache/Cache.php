<?php

namespace edwrodrig\static_generator\cache;


class Cache
{

    /**
     * @var array
     */
    private $index;

    private $cache_hits = [];

    /**
     * @var string
     */
    private $cache_dir;

    public function __construct(string $cache_dir) {
        $this->cache_dir = $cache_dir;
        $this->index = [];

        $this->load_index();
    }

    public function load_index() {
        $filename = $this->get_index_filename();
        if ( file_exists($filename) ) {
            $index_data = file_get_contents($filename);
            if ( $index_data = json_decode($index_data, true) ) {
                $this->index = $index_data;
            }
        }
    }

    public function get_index_filename() : string {
        return $this->cache_dir . '/index.json';
    }


    public function update_cache(CacheItem $entry) : CacheItem {

        $this->cache_hits[$entry->get_cache_key()] = 1;

        if ( !isset($this->index[$entry->get_cache_key()]) ) {
            return $this->set_cache($entry);
        } else {
            $last_entry = $this->index[$entry->get_cache_key()];
            if ( $last_entry->get_last_modification_time() < $entry->get_last_modification_time() ) {
                $last_entry->cache_remove();

                return $this->set_cache($entry);
            } else {
                return $last_entry;
            }
        }
    }

    protected function set_cache(CacheItem $entry) : CacheItem {
        $this->index[$entry->get_cache_key()] = $entry;
        $entry->cache_generate();
        return $entry;
    }

    public function is_hitted(CacheItem $entry) {
        return isset($this->cache_hits[$entry->get_cache_key()]);
    }

    protected function clear_cache_entry(CacheItem $entry) {
        unset($this->cache_hits[$entry->get_key()]);
        $entry->cache_remove();
    }

    public function save_index()
    {
        foreach ($this->index as $id => $entry) {
            if ( $this->is_hitted($entry))
                continue;
            else
                $this->clear_cache_entry($entry);
        }

        file_put_contents(
            $this->get_index_filename(),
            json_encode($this->index, JSON_PRETTY_PRINT)
        );
    }
}
