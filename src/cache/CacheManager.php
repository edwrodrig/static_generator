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
    protected $target_root_path;




    public function __construct(string $cache_dir, Context $context) {
        $this->context = $context;
        $this->target_root_path = $cache_dir;
        $this->index = [];

        $this->index = new CacheIndex($this->get_index_filename());
    }

    /**
     * Get the target root path.
     *
     * Return the cache target root path relative to the {@see Context::getTargetRootPath() context root path}.
     * @return string
     */
    public function getTargetRootPath() : string {
        return $this->target_root_path;
    }

    /**
     * Get the target absolute path.
     *
     * Return the cache target absolute path in the current file system.
     * @return string
     */
    public function getTargetAbsolutePath() : string {
        return $this->context->getTargetRootPath() . DIRECTORY_SEPARATOR . $this->getTargetRootPath();
    }

    public function get_index_filename() : string {
        return $this->absolute_filename('index.json');
    }


    public function update_cache(CacheableItem $item) : CacheEntry {

        $this->cache_hits[$item->getKey()] = 1;

        if ( !isset($this->index[$item->getKey()]) ) {
            $this->getLogger()->begin(sprintf("New cache entry [%s]...GENERATING\n", $item->getTargetRelativePath()));
            return $this->set_cache($item);
        } else {
            $last_entry = $this->index[$item->getKey()];
            $cached_filename  = $this->cache_filename($last_entry->get_cached_file());
            if ( !file_exists($cached_filename) ) {
                $this->getLogger()->begin(sprintf("Cache file removed [%s]...UPDATED\n", $item->getTargetRelativePath()));
                return $this->set_cache($item);
            } else if ( $last_entry->get_generation_date() < $item->getLastModificationTime() ) {
                unlink($cached_filename);
                $this->getLogger()->begin(sprintf("Outdated cache entry [%s]...UPDATED\n", $item->getTargetRelativePath()));
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
        $entry = CacheEntry::createFromItem($item);
        $this->index[$item->getKey()] = $entry;
        $item->generate($this);
        return $entry;
    }

    public function is_hitted($entry) {
        return isset($this->cache_hits[$entry->get_cache_key()]);
    }

    protected function clearEntry(CacheEntry $entry) {
        unset($this->cache_hits[$entry->getKey()]);

        unlink($this->cache_filename($entry->getRelativePath()));
    }

    public function absolute_filename($filename) {
        $absolute_filename = $this->target_root_path . DIRECTORY_SEPARATOR . $filename;
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
