<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:23
 */

namespace edwrodrig\static_generator\cache;

use DateTime;
use Exception;
use JsonSerializable;

/**
 * Class CacheEntry
 * This represent a entry in the {@see CacheIndex cache index}.
 * @package edwrodrig\static_generator\cache
 * @api
 */
class CacheEntry implements JsonSerializable
{
    /**
     * The date when the item was modified last
     *
     * Internally this date should be always equals to the {@see CacheableItem::getLastModificationDate()}
     * @var DateTime
     */
    protected DateTime $last_modification_time;

    /**
     * The relative path filename that where cached.
     * @var string
     */
    protected string $relative_path;

    /**
     * The very identifier of this cache entry.
     *
     * It must be unique between all entries in the {@see CacheIndex index}
     * @var string
     */
    protected string $key;

    /**
     * @var CacheManager $manager
     */
    protected CacheManager $manager;

    /**
     * Cache Additional data.
     *
     * @see CacheableItem::getAdditionalData()
     * @var array
     */
    protected array $data = [];

    /**
     * CacheEntry constructor.
     *
     * To create a entry you must use {@see CacheEntry::createFromItem}
     * @internal
     * @param string $key
     * @param CacheManager $manager
     */
    private function __construct(string $key, CacheManager $manager) {
        $this->key = $key;
        $this->manager = $manager;
    }

    /**
     * Get the unique key of this entry.
     * @api
     * @see CacheEntry::$key
     * @return string
     */
    public function getKey() : string {
        return $this->key;
    }

    /**
     * Creates an entry from a CacheableItem
     * @api
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
     * @throws Exception
     * @internal
     * @internal this method must always be created by the index
     */
    public static function createFromArray(array $data, CacheManager $manager) {
        $entry = new CacheEntry($data['key'], $manager);

        $entry->relative_path = $data['relative_path'];
        $entry->last_modification_time = new DateTime();
        $entry->last_modification_time->setTimestamp((int)$data['last_modification_time']);
        $entry->data = $data['data'];
        return $entry;
    }

    /**
     * Get the target path relative to {@see CacheManager::getTargetAbsolutePath() cache dir}
     * @api
     * @return string
     */
    public function getTargetRelativePath() : string {
        return $this->relative_path;
    }

    /**
     * Get addition data of this entry.
     *
     * This can vary depend on the original CacheableItem
     *
     * @api
     * @see CacheableItem::getAdditionalData()
     * @return array
     */
    public function getAdditionalData() : array {
        return $this->data;
    }

    /**
     * Get the target absolute path
     *
     * The path there the cached file is stored in the local file system.
     * @return string
     */
    protected function getTargetAbsolutePath() : string {
        return $this->manager->getTargetAbsolutePath() . DIRECTORY_SEPARATOR . $this->getTargetRelativePath();
    }

    /**
     * Get the url of the cached entry.
     *
     * Use this function when you want to instantiate the file in the target files, like in a img src tag.
     * ```
     * <img src="<?=$cache_entry->getUrl()?>">
     * ```
     * @api
     * @return string
     */
    public function getUrl() : string {
        return $this->manager->getContext()->getUrl('/' . $this->manager->getTargetWebPath() . DIRECTORY_SEPARATOR . $this->getTargetRelativePath());
    }

    /**
     * Check if the cached file still exists.
     *
     * Maybe sometimes you delete generated cached files manually, so you need to check it to do a restoration.
     * @return bool
     */
    protected function cachedFileExists() : bool {
        return isset($this->relative_path) && file_exists($this->getTargetAbsolutePath());
    }

    /**
     * Remove the cached file.
     *
     * Function to remove the current cached file.
     * This function is used when the entry is updated and the file if replaced by another one.
     * As the files can have different names, the last one must be deleted.
     * @api
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
     * @uses CacheEntry::generate() To generate the file
     * @internal
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

    /**
     * Generate the new cache file.
     *
     * This function replaces the current information of the cache entry with the.
     * Is protected because generation must occurs only when file is modified or not existant.
     * @param CacheableItem $item
     */
    protected function generate(CacheableItem $item) {
        $this->removeCachedFile();

        $this->manager->getLogger()->begin(sprintf('Generating cache file [%s]...', $item->getTargetRelativePath()));
            //generate must be the first action
            $item->generate($this->manager);

            //because other must depend on data generated there, specially getAdditional data
            $this->last_modification_time = $item->getLastModificationTime();
            $this->relative_path = $item->getTargetRelativePath();
            $this->data = $item->getAdditionalData();
        $this->manager->getLogger()->end('GENERATED', false);

    }

    /**
     * The json version of the data.
     *
     * This data must be compatible with {@see CacheEntry::createFromArray()}
     * @internal
     * @return array
     */
    public function jsonSerialize() : array {
        return [
            'key' => $this->key,
            'last_modification_time' => $this->last_modification_time->getTimestamp(),
            'relative_path' => $this->relative_path,
            'data' => $this->data
        ];
    }


    /**
     * String function.
     *
     * Auto return the {@see CacheEntry::getUrl() url} in string context.
     * ```
     * <img src="<?=$cache_entry?>">
     * ```
     * @api
     * @return string
     */
    public function __toString() : string {
        return $this->getUrl();
    }
}