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
 * Interface CacheableItem
 * An interface that must implement every Item suitable for caching.
 * This is readed from {@see CacheEntry::createFromItem() the index} to create an cache entry}
 * @package edwrodrig\static_generator\cache
 */
interface CacheableItem
{
    /**
     * An identifier of the item in the index.
     *
     * Should be unique inside a {@see CacheIndex cache index}.
     * In most cases a {@see getTargetRelativePath() relative path} works as a key,
     * but if you do some {@see https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching#invalidating_and_updating_cached_responses caching management with keys}
     * it is recommended that this key is different from {@see getTargetRelativePath() relative path}
     * @see CacheEntry::getKey()
     * @return string
     */
    public function getKey() : string;

    /**
     * Return the last time the item was modified.
     *
     * It is use to determine if the file has changed from previous time.
     * @return DateTime
     */
    public function getLastModificationTime() : DateTime;

    /**
     * Get the target relative path.
     *
     * This must return the target relative path to the {@see CacheManager::getTargetRootPath() target root path of the context}
     * Example you want to generate a file as cache/folder/img_24x24.jpg.
     * Sometimes is a good idea that the file is salted to
     * {@see https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching#invalidating_and_updating_cached_responses help caching efficience}
     * @return string
     */
    public function getTargetRelativePath() : string;

    /**
     * Generates this cache entry.
     *
     * This must generate a cache file. All cache files must be generated in to {@see CacheManager::getTargetAbsolutePath() target root path}.
     * For convenience this method should call {@see CacheManager::prepareCacheFile()} to get the target filename.
     * This function should not delete the previous entry, because it is removed internally.
     * ```
     *   $absolute_path = $manager->prepareCacheFile();
     *   file_put_contents($absolute_path, 'content');
     * ```
     *
     * @param CacheManager $manager to retrieve the {@see CacheManager::prepareCacheFile() target root path}
     */
    public function generate(CacheManager $manager);
}