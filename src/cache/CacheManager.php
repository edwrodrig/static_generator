<?php

namespace edwrodrig\static_generator\cache;


use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\util\Logger;

/**
 * Class CacheManager
 *
 * This function contains works as an interface to {@see CacheManager::update() generate} cache entries from {@see CacheableItem cacheable items}.
 * When caches are used in a context, they must be registered using {@see Context::registerContext()}
 * @api User must interact with this class
 * @package edwrodrig\static_generator\cache
 */
class CacheManager
{
    /**
     * The generation context of this cache
     * @var Context
     */
    private $context = null;

    /**
     * @var CacheIndex
     */
    private $index;

    /**
     * The cache target root path absolute.
     * @var string
     */
    protected $target_root_path;

    /**
     * The target web path of the cache relative to the {@see Context::getTargetWebRoot() web root} of the context.
     * This value must be unique between caches in a {@see Context::registerContext() context}.
     * @var string
     */
    protected $target_web_path = "cache";


    /**
     * CacheManager constructor.
     * @api
     * @param string $target_root_path {@see CacheManager::$target_root_path}
     * @param Context $context
     */
    public function __construct(string $target_root_path) {
        $this->target_root_path = $target_root_path;
        $this->index = new CacheIndex($this);
    }

    /**
     * Get the context of the cache.
     * @return Context
     */
    public function getContext() : Context {
        return $this->context;
    }

    /**
     * Set a new context.
     *
     * Useful when a cache is shared between different contexts
     * @param Context $context
     * @return CacheManager
     */
    public function setContext(Context $context) : CacheManager {
        $this->context = $context;
        return $this;
    }

    /**
     * Get the target web path of the cacha manager.
     *
     * @see CacheManager::$target_web_path
     * @return string
     */
    public function getTargetWebPath() : string {
        return $this->target_web_path;
    }

    /**
     * @param string $target_web_path
     * @return $this
     */
    public function setTargetWebPath(string $target_web_path) {
        $this->target_web_path = $target_web_path;
        return $this;
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
        return $this->target_root_path;
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

    /**
     * Creates a symlink to the target output.
     *
     * If you don't do this the cached files will be not visible in the target output.
     */
    public function linkToTarget() {
        symlink(
            $this->getTargetAbsolutePath(),
            $this->context->getTargetRootPath() . DIRECTORY_SEPARATOR . $this->getTargetWebPath()
        );
    }
}
