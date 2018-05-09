<?php

namespace edwrodrig\static_generator\cache;


use edwrodrig\static_generator\Context;

class CacheManager
{
    /**
     * @var Context
     */
    private $context;
    /**
     * @var array
     */
    private $index;

    private $cache_hits = [];

    /**
     * @var string
     */
    protected $cache_dir;

    public function __construct(string $cache_dir, Context $context) {
        $this->context = $context;
        $this->cache_dir = $cache_dir;
        $this->index = [];

        $this->loadIndex();
    }

    public function get_index_filename() : string {
        return $this->absolute_filename('index.json');
    }


    public function update_cache(CacheableItem $item) : CacheEntry {

        $this->cache_hits[$item->get_cache_key()] = 1;

        if ( !isset($this->index[$item->get_cache_key()]) ) {
            $this->getLogger()->begin(sprintf("New cache entry [%s]...GENERATING\n", $item->get_cached_file()));
            return $this->set_cache($item);
        } else {
            $last_entry = $this->index[$item->get_cache_key()];
            $cached_filename  = $this->cache_filename($last_entry->get_cached_file());
            if ( !file_exists($cached_filename) ) {
                $this->getLogger()->begin(sprintf("Cache file removed [%s]...UPDATED\n", $item->get_cached_file()));
                return $this->set_cache($item);
            } else if ( $last_entry->get_generation_date() < $item->get_last_modification_time() ) {
                unlink($cached_filename);
                $this->getLogger()->begin(sprintf("Outdated cache entry [%s]...UPDATED\n", $item->get_cached_file()));
                return $this->set_cache($item);
            } else {
                //Page::log(sprintf("Cache hit[%s]...RETRIEVED\n", $entry->get_cached_file()));
                return $last_entry;
            }
        }
    }

    public function cache_filename($filename) {
        return $this->absolute_filename('files' . DIRECTORY_SEPARATOR . $filename);
    }

    protected function set_cache(CacheableItem $item) : CacheEntry {
        $entry = CacheEntry::create_from_item($item);
        $this->index[$item->get_cache_key()] = $entry;
        $item->cache_generate($this);
        return $entry;
    }

    public function is_hitted($entry) {
        return isset($this->cache_hits[$entry->get_cache_key()]);
    }

    protected function clearEntry(CacheEntry $entry) {
        unset($this->cache_hits[$entry->get_cache_key()]);

        unlink($this->cache_filename($entry->get_cached_file()));
    }

    public function absolute_filename($filename) {
        $absolute_filename = $this->cache_dir . DIRECTORY_SEPARATOR . $filename;
        @mkdir(dirname($absolute_filename), 0777, true);
        return $absolute_filename;
    }

    public function save_index()
    {
        foreach ($this->index as $id => $entry) {
            if ( $this->is_hitted($entry))
                continue;

            $this->getLogger()->begin(sprintf("Unused cache entry [%s]...", $entry->get_cached_file()));
            $this->clearEntry($entry);
            $this->getLogger()->end('REMOVED', false);
        }

        file_put_contents(
            $this->get_index_filename(),
            json_encode($this->index, JSON_PRETTY_PRINT)
        );
    }

    public function link_cached(string $source, string $target) {
        $this->getLogger()->begin(sprintf("Linking cache files [%s] > [%s]...", $source, $target));
        $target = Site::get()->output($target);
        @mkdir(dirname($target), 0777, true);
        passthru(sprintf('cp -al %s %s', $this->cache_filename($source), $target));
        $this->getLogger()->end('LINKED', false);
    }

    /**
     * @return Logger
     */
    protected function getLogger() : Logger {
        return $this->context->getLogger();
    }
}
