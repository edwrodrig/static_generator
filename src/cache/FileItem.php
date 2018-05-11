<?php

namespace edwrodrig\static_generator\cache;

use DateTime;

class FileItem implements CacheableItem
{
    private $root_path;
    protected $filename;

    /**
     * The version of the file.
     *
     * Files may have different version. For example and image can be the original image and a thumbnail version.
     * This variable is a way to differentiate them. This value are used to generate a the {@see FileItem::getKey() key}
     * @var string
     */
    protected $version;

    /**
     * Target extension.
     *
     * Generally the source an target have the same extension. But in some case there processing are involved the might be different.
     * For example and bmp source should generate a jpg target
     * @var string
     */
    protected $target_extension = '';

    /**
     * Salt added to the target relative path.
     *
     * Helps to differentiate generations of a file in different times.
     * It is useful for advanced {@see https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching#invalidating_and_updating_cached_responses caching techniques}.
     * @var string
     */
    protected $salt = '';

    /**
     * FileItem constructor.
     *
     * @param string $root_path
     * @param string $filename
     * @param string $version
     */
    public function __construct(string $root_path, string $filename, string $version = '') {
        $this->root_path = $root_path;
        $this->filename = $filename;
        $this->version = $version;
    }

    /**
     * Generates a random salt fot the file.
     *
     * @see FileItem::setSalt()
     * @return FileItem
     */
    public function setSalt() : FileItem {
        $this->salt = uniqid();
        return $this;
    }

    /**
     * Set the target extension
     *
     * @see FileItem::$target_extension
     * @param string $extension
     * @return FileItem
     */
    public function setTargetExtension(string $extension) : FileItem {
        $this->target_extension = $extension;
        return $this;
    }

    public function getKey() : string {
        $base_name = self::getBasename($this->filename);

        if ( empty($this->version) )
            return $base_name;
        else
            return $base_name . '_' . $this->version;
    }

    protected function getSourceFilename() : string{
        return $this->root_path . DIRECTORY_SEPARATOR . $this->filename;
    }

    public function getLastModificationTime() : DateTime {
        $date = new DateTime();
        $date->setTimestamp(filemtime($this->getSourceFilename()));
        return $date;
    }

    /**
     * Get the target file extension.
     *
     * Generally the source an target have the same extension. But in some case there processing are involved the might be different.
     * For example and bmp source should generate a jpg target
     * @return string
     */
    protected function getTargetExtension() : string {
        return empty($this->target_extension) ? pathinfo($this->filename, PATHINFO_EXTENSION) : $this->target_extension;
    }

    /**
     * Get the Target relative path.
     *
     * The target filename is the same as the source {@see FileItem::$filename} {@see FileItem::setSalt() salted} and with a {@see FileItem::getTargetExtension() overriden extension}
     * @return string
     */
    public function getTargetRelativePath() : string {
        $file = $this->getKey();

        if ( !empty($this->salt) )
            $file .= '_' . $this->salt;

        $extension = $this->getTargetExtension();

        if ( empty($extension) )
            return $file;
        else
            return $file . '.' . $extension;
    }

    public function generate(CacheManager $manager) {
        copy(
            $this->getSourceFilename(),
            $manager->prepareCacheFile($this)
        );
    }

    /**
     * Get the basename without extension.
     *
     * If filename is foo/bar.jpg then this function output foo/bar
     * @param string $filename
     * @return string
     */
    public static function getBasename(string $filename) : string {
        $info = pathinfo($filename);
        if ( $info['dirname'] == '.' )
            $info['dirname'] = '';
        else
            $info['dirname'] = $info['dirname'] . DIRECTORY_SEPARATOR;

        // filename has the name of the file without the last extension
        return $info['dirname'] . $info['filename'];
    }
}