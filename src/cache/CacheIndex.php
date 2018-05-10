<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\cache;

/**
 * Class CacheIndex
 * @package edwrodrig\static_generator\cache
 * @internal
 */
class CacheIndex
{
    /**
     * @var CacheEntry[]
     */
    private $data = [];

    /**
     * The base path of this cache index
     * @var string
     */
    private $path;


    private $hits = [];

    /**
     * @var CacheManager
     */
    private $manager;


    /**
     * CacheIndex constructor.
     * Creates a cache index object
     * @param string $filename
     * @param CacheManager $manager
     */
    public function __construct(string $filename, CacheManager $manager) {

        $filename = $manager->getTargetAbsolutePath() . DIRECTORY_SEPARATOR . $filename;
        $this->manager = $manager;
        $this->path = dirname($filename);
        if ( !file_exists($filename) ) return;

        $index_data = file_get_contents($filename);
        if ( $index_data = json_decode($index_data, true) ) {
            foreach ( $index_data as $cache_key => $entry_data ) {
                /** @noinspection PhpInternalEntityUsedInspection */
                $this->data[$cache_key] = CacheEntry::createFromArray($entry_data, $this->manager);
            }
        }
    }

    /**
     * Update the index according a item
     *
     * This function always creates a new entry or update an existing one if needed
     * @param CacheableItem $item
     * @return CacheEntry an updated cache entry
     */
    public function update(CacheableItem $item) : CacheEntry {
        $this->hits[$item->getKey()] = 1;

        if ( isset($this->data[$item->getKey()]) ) {
            $entry = $this->data[$item->getKey()];

            /** @noinspection PhpInternalEntityUsedInspection */
            $entry->update($item);
            return $entry;

        } else {
            $this->manager->getLogger()->begin(sprintf("New cache entry [%s]", $item->getKey()));
                $entry = CacheEntry::createFromItem($item, $this->manager);
                $this->data[$item->getKey()] = $entry;
            $this->manager->getLogger()->end('', false);
            return $entry;
        }

    }

    protected function removeEntry(CacheEntry $entry) {
        $entry->removeCachedFile();
        unset($this->data[$entry->getKey()]);
    }

    /**
     *
     */
    public function removeUnusedEntries() {
        foreach ($this->data as $key => $entry) {
            if (isset($this->hits[$key]))
                continue;

            $this->manager->getLogger()->begin(sprintf("Unused cache entry [%s] FOUND!", $entry->getKey()));
            $this->removeEntry($entry);
            $this->manager->getLogger()->end('', false);
        }
    }
}