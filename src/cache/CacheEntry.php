<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:23
 */

namespace edwrodrig\static_generator\cache;

use DateTime;
use JsonSerializable;

/**
 * Class CacheEntry
 * This represent a entry in the {@see CacheIndex cache index}.
 * @package edwrodrig\static_generator\cache
 */
class CacheEntry implements JsonSerializable
{
    /**
     * The date when the item was modified last
     *
     * Internally this date should be always equals to the {@see CacheableItem::getLastModificationDate()}
     * @var DateTime
     */
    protected $last_modification_time;

    /**
     * The relative path filename that where cached.
     * @var string
     */
    protected $relative_path;

    /**
     * The very identifier of this cache entry.
     *
     * It must be unique between all entries in the {@see CacheIndex index}
     * @var string
     */
    protected $key;

    /**
     * @var CacheManager $manager
     */
    protected $manager;

    /**
     * CacheEntry constructor.
     *
     * To create a entry you must use {@see CacheEntry::createFromItem}
     * @param string $key
     * @param CacheManager $manager
     */
    private function __construct(string $key, CacheManager $manager) {
        $this->key = $key;
        $this->manager = $manager;
    }

    /**
     * Get the unique key of this entry.
     *
     * @see CacheEntry::$key
     * @return string
     */
    public function getKey() : string {
        return $this->key;
    }

    /**
     * Creates an entry from a CacheableItem
     * @param CacheableItem $item
     * @param CacheManager $manager
     * @return CacheEntry
     */
    public static function createFromItem(CacheableItem $item, CacheManager $manager) {
        $entry = new CacheEntry($item->getKey(), $manager);
        $entry->generate($item);

        return $entry;
    }

    /**
     * Creates a cache entry from an recovered {@see CacheEntry::jsonSerialize() array entry}
     * @param array $data
     * @param CacheManager $manager
     * @return CacheEntry
     * @internal this method must always be created by the index
     */
    public static function createFromArray(array $data, CacheManager $manager) {
        $entry = new CacheEntry($data['key'], $manager);

        $entry->relative_path = $data['relative_path'];
        $entry->last_modification_time = new DateTime();
        $entry->last_modification_time->setTimestamp((int)$data['last_modification_time']);
        return $entry;
    }

    /**
     *
     * @return string
     */
    public function getTargetRelativePath() : string {
        return $this->relative_path;
    }

    /**
     * Get the target absolute path
     *
     * The path there the cached file is stored in the local file system.
     * @return string
     */
    public function getTargetAbsolutePath() : string {
        return $this->manager->getTargetAbsolutePath() . DIRECTORY_SEPARATOR . $this->getTargetRelativePath();
    }

    /**
     * Check if the cached file still exists.
     *
     * Maybe sometimes you delete generated cached files manually, so you need to check it to do a restoration.
     * @return bool
     */
    protected function cachedFileExists() : bool {
        return !is_null($this->relative_path) && file_exists($this->getTargetAbsolutePath());
    }

    /**
     * Remove the cached file.
     *
     * Function to remove the current cached file.
     * This function is used when the entry is updated and the file if replaced by another one.
     * As the files can have different names, the last one must be deleted.
     * @return bool
     */
    public function removeCachedFile() : bool
    {
        if ($this->cachedFileExists()) {
            $this->manager->getLogger()->begin(sprintf('Removing file [%s]...', $this->getTargetRelativePath()));
            unlink($this->getTargetAbsolutePath());
            $this->manager->getLogger()->end('REMOVED', false);
            return true;
        }
        return false;
    }

    /**
     * Updated the entry
     *
     * Only updates if the entry {@see CacheEntry::cachedFileExists() losses} their cached filename (example: by manually deleting the cache file)
     * or if cache entry is {@see CacheableItem::getLastModificationTime() outdated}
     * @param CacheableItem $item
     * @return bool If an update occurs
     */
    public function update(CacheableItem $item) {

        if ( !$this->cachedFileExists() ) {
            $this->manager->getLogger()->begin(sprintf('Cache file [%s] NOT FOUND!', $this->getTargetRelativePath()));
            $this->generate($item);
            $this->manager->getLogger()->end('', false);
            return true;

        } else if ( $this->last_modification_time < $item->getLastModificationTime() ) {
            $this->manager->getLogger()->begin(sprintf('Outdated cache entry [%s] FOUND!', $item->getKey()));
            $this->generate($item);
            $this->manager->getLogger()->end('', false);
            return true;

        } else {
            return false;

        }
    }


    protected function generate(CacheableItem $item) {
        $this->removeCachedFile();
        $this->last_modification_time = $item->getLastModificationTime();
        $this->relative_path = $item->getTargetRelativePath();

        $this->manager->getLogger()->begin(sprintf('Generating cache file [%s]...', $item->getTargetRelativePath()));
            $item->generate($this->manager);
        $this->manager->getLogger()->end('GENERATED', false);
    }

    /**
     * The json version of the data.
     *
     * This data must be compatible with {@see CacheEntry::createFromArray()}
     *
     * @return array
     */
    public function jsonSerialize() : array {
        return [
            'key' => $this->key,
            'last_modification_time' => $this->last_modification_time->getTimestamp(),
            'relative_path' => $this->relative_path
        ];
    }

    /**
     * Get the last modification date
     * @return DateTime
     */
    public function getLastModificationTime(): DateTime
    {
        return $this->last_modification_time;
    }
}