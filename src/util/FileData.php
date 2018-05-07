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
     * The nesting of the current file, generally associated with a the folder depth
     * @var int
     */
    private $nesting_level = 0;

    /**
     * The path of the file relative to some {@see FileData::getRootPath() root}.
     * Relative path mush not hav
     * @var string
     */
    private $relative_path;

    /**
     * Absolute root path from where from where {@see FileData::getAbsolutePath() this file} is {@see FileData::getRelativePath() relative}
     * @var string|null
     */
    private $root_path;


    /**
     * FileData constructor.
     * @param int $nesting_level {@see FileData::$nesting_level}
     * @param string $relative_path {@see FileData::$relative_path}
     * @param string|null $root_path {@see FileData::$root_path}
     */
    public function __construct(int $nesting_level = 0, string $relative_path, string $root_path = null) {

        $this->relative_path = $relative_path;
        $this->root_path = $root_path;
        $this->nesting_level = $nesting_level;
    }

    /**
     * Get the absolute path of the file.
     * Reutnr null if the input file is not existant
     * @return null|string
     */
    public function getAbsolutePath() : ?string {
        if ( is_null($this->root_path ))
            return null;
        else
            return $this->root_path . DIRECTORY_SEPARATOR . $this->relative_path;
    }

    /**
     * Get the path of the file relative to some {@see FileData::getRootPath() root}.
     * @return string
     */
    public function getRelativePath() : string {
        return $this->relative_path;
    }

    /**
     * Get the {@see FileData::$root_path absolute root path}
     * @return string|null
     */
    public function getRootPath() : ?string {
        return $this->root_path;
    }

    /**
     * Return the nesting level.
     * @return int {@see FileData::$nesting_level}
     */
    public function getNestingLevel() : int {
        return $this->nesting_level;
    }

    /**
     * Checks if the file exists.
     * @uses file_exists()
     * @return bool
     */
    public function exists() : bool {
        $absolute_path = $this->getAbsolutePath();

        if ( is_null($absolute_path))
            return false;
        else
            return file_exists($this->getAbsolutePath());
    }

    public function isScss() : bool {
        return preg_match('/\.scss$/', $this->relative_path) === 1;
    }

    public function isPhp() : bool {
        return preg_match('/\.php$/', $this->relative_path) === 1;
    }

    public function isIgnore() : bool {
        return preg_match('/\.swp$/', $this->relative_path) === 1;
    }

    /**
     *
     * @return string A Class name for the generator
     */
    public function getGenerationClassName() : string {
        if ( $this->isPhp() ) {
            return PagePhp::class;
        } else if ( $this->isScss() ) {
            return PageScss::class;
        } else if ( $this->isIgnore() ) {
            return null;
        } else {
            return PageCopy::class;
        }
    }

    public function createChildData(string $relative_path) : FileData {
        return new FileData(
            $this->nesting_level + 1,
            $relative_path,
            $this->getRootPath()
        );
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

                    $file_data = $this->createChildData($file);

                    yield from $file_data;
                }
            }
        }
    }

    /**
     * Get the contents of the file.
     *
     * Return an empty string when the file does not exists
     * @return string
     */
    public function getFileContents() : string {
        $file = $this->getAbsolutePath();
        if ( is_null($file) )
            return '';
        else
            return file_get_contents($file);
    }
}