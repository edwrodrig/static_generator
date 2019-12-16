<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;

use edwrodrig\file_cache\CacheManager;

/**
 * Class PageFile
 *
 * This is a base class for all files that generates from a source file.
 * For example when you process a {@see PagePhp php file} then is transformed in and html text file, or just a {@see PageCopy plain copy}
 * @package edwrodrig\static_generator
 * @api
 */
abstract class PageFile extends Page
{

    public function __construct(string $source_path, Context $context) {
        parent::__construct($source_path, $context);
    }

    /**
     * Get the absolute path of the source file.
     *
     * Return null if the input file is not existant
     * @api
     * @return string
     */
    public function getSourceAbsolutePath() : string {
        return $this->context->getSourceRootPath() . DIRECTORY_SEPARATOR . $this->getSourceRelativePath();
    }

    /**
     * Get the source relative path.
     *
     * In most cases match the {Page::getTargetRelativePath() target relative path}
     * @api
     * @return string
     */
    public function getSourceRelativePath() : string {
        return $this->relative_path;
    }

    /**
     * Get the contents of the file.
     *
     * Return an empty string it the file does not exist.
     * The file should always exist.
     * @api
     * @return string
     */
    public function getSourceFileContents() : string {
        $file = $this->getSourceAbsolutePath();

        if ( file_exists($file) )
            return file_get_contents($file);
        else
            return "";
    }

    /**
     * Copy the source file to the target file.
     *
     * This is used in {@see PageCopy} and in {@see PagePhp::isRaw() raw php pages}
     * @api
     * @throws exception\CopyException
     */
    protected function copyPage() {
        $source = $this->getSourceAbsolutePath();
        $target = $this->getTargetAbsolutePath();
        @mkdir(dirname($target), 0777, true);
        $this->getLogger()->begin(sprintf("Copying file [%s]...", $this->getTargetRelativePath()));

        if ( !copy($source, $target) ) {
            throw new exception\CopyException('Error at copying');
        }
        $this->getContext()->registerGeneratedPage($this->getTargetRelativePath());
        $this->getLogger()->end("DONE\n");
    }

    /**
     * Get the context cache.
     *
     * Retrieve the {@see Context::getCache() context cache} by {@see CacheManager::getTargetWebPath() web path}
     *
     * @api
     * @param string $web_path
     * @return CacheManager
     * @throws exception\CacheDoesNotExists
     */
    public function getCache(string $web_path) : CacheManager {
        return $this->getContext()->getCache($web_path);
    }
}

