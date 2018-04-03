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
    protected $cache_dir;

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
                foreach ( $index_data as $entry_data ) {
                    $this->index[] = CacheEntry::create_from_array($entry_data);
                }
            }
        }
    }

    public function get_index_filename() : string {
        return $this->absolute_filename('index.json');
    }


    public function update_cache(CacheItem $entry) {

        $this->cache_hits[$entry->get_cache_key()] = 1;

        if ( !isset($this->index[$entry->get_cache_key()]) ) {
            return $this->set_cache($entry);
        } else {
            $last_entry = $this->index[$entry->get_cache_key()];
            if ( $last_entry->get_generation_date() < $entry->get_last_modification_time() ) {
                unlink($this->absolute_filename($last_entry->get_cached_file()));

                return $this->set_cache($entry);
            } else {
                return $last_entry;
            }
        }
    }

    protected function set_cache(CacheItem $item) : CacheItem {
        $this->index[$item->get_cache_key()] = CacheEntry::create_from_item($item);
        $item->cache_generate($this);
        return $item;
    }

    public function is_hitted(CacheItem $entry) {
        return isset($this->cache_hits[$entry->get_cache_key()]);
    }

    protected function clear_cache_entry(CacheEntry $entry) {
        unset($this->cache_hits[$entry->get_cache_key()]);

        unlink($this->absolute_filename($entry->get_cache_file()));
    }

    public function absolute_filename($filename) {
        $absolute_filename = $this->cache_dir . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $filename;
        @mkdir(dirname($absolute_filename), 0777, true);
        return $absolute_filename;
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
