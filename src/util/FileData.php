<?php

namespace edwrodrig\static_generator\util;

use edwrodrig\static_generator\PageCopy;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\PageScss;
use IteratorAggregate;

/**
 * Class PageData
 * This class stores data of a page to generate
 * @package edwrodrig\static_generator
 */
class FileData implements IteratorAggregate
{
    /**
     * The path of the file relative to some {@see FileData::getRootPath() root}.
     * Relative path mush not hav
     * @var string
     */
    private $relative_path;

    /**
     * FileData constructor.
     * @param string $relative_path {@see FileData::$relative_path}
     */
    public function __construct(string $relative_path) {
        $this->relative_path = $relative_path;
    }

    /**
     * Get the path of the file relative to some {@see FileData::getRootPath() root}.
     * @return string
     */
    public function getRelativePath() : string {
        return $this->relative_path;
    }

    public function getBasename() : string {
        return basename($this->relative_path);
    }

    public function isScss() : bool {
        return preg_match('/^[^_].*\.scss$/', $this->getBasename()) === 1;
    }

    public function isPhp() : bool {
        return preg_match('/\.php$/', $this->getBasename()) === 1;
    }

    public function isIgnore() : bool {
        if ( preg_match('/^_.*\.scss$/', $this->getBasename()) === 1)
            return true;
        else if ( preg_match('/\.swp$/', $this->getBasename()) === 1)
            return true;
        else
            return false;
    }

    /**
     *
     * @return string A Class name for the generator
     * @throws exception\IgnoredPageFileException
     */
    public function getGenerationClassName() : string {
        if ( $this->isPhp() ) {
            return PagePhp::class;
        } else if ( $this->isScss() ) {
            return PageScss::class;
        } else if ( $this->isIgnore() ) {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\IgnoredPageFileException($this->getAbsolutePath());
        } else {
            return PageCopy::class;
        }
    }

    /**
     * Iterate files.
     * If this file is a single file then yields itself.
     * If this file is a directory then yields every nested file, if inside there are directories then applies this function recursively.
     * @return \Generator|FileData[]
     */
    public function getIterator() {
        if ( !$this->exists() ) {
            yield $this;
        } else {
            $absolute_path = $this->getAbsolutePath();

            if ( is_file($absolute_path)) {
                if ( !$this->isIgnore() )
                    yield $this;

            } else if ( is_dir($absolute_path) ) {

                foreach (scandir($absolute_path) as $file) {

                    //ignore dot and double dot hard links
                    if ($file == '.') continue;
                    if ($file == '..') continue;

                    $file_data = new FileData($file);

                    yield from $file_data;
                }
            }
        }
    }
}