<?php

namespace edwrodrig\static_generator\cache;


use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\Logger;

/**
 * Class CacheManager
 *
 * This function contains works as an interface to {@see CacheManager::update() generate} cache entries from {@see CacheableItem cacheable items}.
 * @api User must interact with this class
 * @package edwrodrig\static_generator\cache
 */
class CacheManager
{
    /**
     * The generation context of this cache
     * @var Context
     */
    private $context;

    /**
     * @var CacheIndex
     */
    private $index;

    /**
     * The cache target root path relative to the {@see Context::getTargetRootPath() context root path}.
     * @var string
     */
    protected $target_root_path;


    /**
     * CacheManager constructor.
     * @api
     * @param string $target_root_path
     * @param Context $context
     */
    public function __construct(string $target_root_path, Context $context) {
        $this->context = $context;
        $this->target_root_path = $target_root_path;
        $this->index = new CacheIndex($this);
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


    /**
     * Save the cache index.
     *
     * @api
     * @uses CacheIndex::save()
     */
    public function save() {
        $this->index->save();
    }
}
