<?php

namespace edwrodrig\static_generator\cache;


use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\Logger;

/**
 * Class CacheManager
 * @package edwrodrig\static_generator\cache
 * @api
 */
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

    /**
     * @var string
     */
    protected $target_root_path;


    public function __construct(string $cache_dir, Context $context) {
        $this->context = $context;
        $this->target_root_path = $cache_dir;
        $this->index = new CacheIndex('cache_index.json', $this);
    }

    /**
     * Get the target root path.
     *
     * Return the cache target root path relative to the {@see Context::getTargetRootPath() context root path}.
     * @return string
     */
    protected function getTargetRootPath() : string {
        return $this->target_root_path;
    }

    /**
     * Get the target absolute path.
     *
     * Return the cache target absolute path in the current file system.
     *
     * @api
     * @return string
     */
    public function getTargetAbsolutePath() : string {
        return $this->context->getTargetRootPath() . DIRECTORY_SEPARATOR . $this->getTargetRootPath();
    }

    /**
     * Update the index according a item
     *
     * This function always creates a new entry or update an existing one if needed.
     *
     * @api
     * @param CacheableItem $item
     * @return CacheEntry an updated cache entry
     */
    public function update(CacheableItem $item) : CacheEntry {
        return $this->index->update($item);
    }

    /**
     * Prepare a cache file.
     *
     * This functions should always be called before creating a cache file.
     * This create the respective folders if does not exist and return the absolute path to use.
     * This function is intended to be uses in {@see CacheableItem::generate()}.
     *
     * @api
     * @param CacheableItem $item
     * @return string
     */
    public function prepareCacheFile(CacheableItem $item) : string {
        $absolute_path = $this->getTargetAbsolutePath() . DIRECTORY_SEPARATOR . $item->getTargetRelativePath();
        @mkdir(dirname($absolute_path), 0777, true);
        return $absolute_path;
    }

    /**
     * Get the context logger.
     *
     * its a convenience function for internal cache element has access to the log.
     * You can use it in {@see CacheableItem::generate()}.
     *
     * @api
     * @return Logger
     */
    public function getLogger() : Logger {
        return $this->context->getLogger();
    }
}
