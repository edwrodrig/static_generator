<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\cache;

/**
 * Class CacheIndex
 *
 * This class is intended to be used by cache manager.
 * There is no need that final users subclass this object
 * @package edwrodrig\static_generator\cache
 * @internal
 */
class CacheIndex
{
    /**
     * The cache entries in the form of an associative array where the {@see CacheEntry::getKey() key} references the {@see CacheEntry}
     * @var CacheEntry[]
     */
    private $data = [];


    /**
     * Hits of keys
     *
     * This is a associative array with {@see CacheEntry::getKey() keys} and 1 as values.
     * Every key present in this array means taht that key was queried.
     * If a key is not present in this array at the end of a generation means that it is {@see CacheIndex::removeUnusedEntries() unused}.
     * @var array
     */
    private $hits = [];

    /**
     * The cache manager.
     *
     * Important to retrieve some pass it to cache entries
     * @var CacheManager
     */
    private $manager;

    /**
     * CacheIndex constructor.
     * Creates a cache index object
     * @param CacheManager $manager
     */
    public function __construct(CacheManager $manager) {

        $this->manager = $manager;
        $filename = $this->getIndexAbsoluteFilePath();
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

    /**
     * Removes a entry from the index
     *
     * @uses CacheEntry::removeCachedFile()
     * @param CacheEntry $entry
     */
    protected function removeEntry(CacheEntry $entry) {
        $entry->removeCachedFile();
        unset($this->data[$entry->getKey()]);
    }

    /**
     * Removes unused entries.
     *
     * All the entries without {@see CacheIndex::$hits hits} are going to be removed.
     * This function should be called before saving the index to a file.
     * Using this functions reset {@see CacheIndex::$hits previous hits}.
     */
    protected function removeUnusedEntries() {
        foreach ($this->data as $key => $entry) {
            if (isset($this->hits[$key]))
                continue;

            $this->manager->getLogger()->begin(sprintf("Unused cache entry [%s] FOUND!", $entry->getKey()));
            $this->removeEntry($entry);
            $this->manager->getLogger()->end('', false);
        }


        $this->hits = [];
    }

    /**
     * Get the absolute file path of the index.
     *
     * @return string
     */
    protected function getIndexAbsoluteFilePath() : string {
        return $this->manager->getTargetAbsolutePath() . DIRECTORY_SEPARATOR . '.cache_index.json';
    }

    /**
     * Save the index in a file.
     *
     * @uses CacheIndex::removeUnusedEntries()
     */
    public function save() : void {
        $this->removeUnusedEntries();
        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($this->getIndexAbsoluteFilePath(), $json);
    }
}