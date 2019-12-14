<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\cache;

use DateTime;

/**
 * Class FileItem
 *
 * Use this function to cache files.
 * This class works with a {@see CacheManager cache} in the following way.
 * ```
 * $file = new FileItem('/documents', 'doc_1.pdf');
 * $cache_manager->update($file);
 * ```
 *
 * Maybe you want to override {@see FileItem::generate()} to creating files with other behaviours lije {@see ImageItem}
 * @api
 * @see FileItem::setSalt() to set a salt to the target filename
 * @package edwrodrig\static_generator\cache
 */
class FileItem implements CacheableItem
{
    /**
     * The root path of the file.
     *
     * The absolute path must be splitted in two parts The root path and the {@see FileItem::$filename filename}.
     * This part is all the part of the path not considered in the target cache dir.
     *
     * In the following case
     * ```
     * $root_path = '/some/root/path'
     * $filename = 'files/used.txt'
     * ````
     *
     * The target cache path will be cache/root/files/used.txt
     *
     * @var string
     */
    private string $root_path;

    /**
     * The filename
     *
     * The path to the source filename relative to the {@see FileItem::$root_path}
     * This also works as a relative path to the target dir.
     *
     * @var string
     */
    protected string $filename;

    /**
     * The version of the file.
     *
     * Files may have different version. For example and image can be the original image and a thumbnail version.
     * This variable is a way to differentiate them. This value are used to generate a the {@see FileItem::getKey() key}
     * @var string
     */
    protected string $version;

    /**
     * Target extension.
     *
     * Generally the source an target have the same extension. But in some case there processing are involved the might be different.
     * For example and bmp source should generate a jpg target
     * @var string
     */
    protected string $target_extension = '';

    /**
     * Salt added to the target relative path.
     *
     * Helps to differentiate generations of a file in different times.
     * It is useful for advanced {@see https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching#invalidating_and_updating_cached_responses caching techniques}.
     * @var string
     */
    protected string $salt = '';

    /**
     * FileItem constructor.
     *
     * @api
     * @param string $root_path {@see FileItem::$root_path}
     * @param string $filename {@see FileItem::$filename}
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
     * @see FileItem::$salt
     * @api
     * @return FileItem
     */
    public function setSalt() : FileItem {
        $this->salt = uniqid();
        return $this;
    }

    /**
     * Set the target extension.
     *
     * This is useful when you want to override the extension of the file.
     * For example if you want to change the extension of a image from jpg to png.
     * This change works only on the {@see FileItem::getTargetRelativeName() target name}.
     * The effective conversion of the file should be implemented in the {@see FileItem::generate() method}.
     *
     * @see FileItem::$target_extension
     * @api
     * @param string $extension
     * @return FileItem
     */
    public function setTargetExtension(string $extension) : FileItem {
        $this->target_extension = $extension;
        return $this;
    }

    /**
     * An identifier of the file in the index.
     *
     * @api
     * @return string
     */
    public function getKey() : string {
        $base_name = self::getBasename($this->filename);

        if ( empty($this->version) )
            return $base_name;
        else
            return $base_name . '_' . $this->version;
    }

    /**
     * Get the absolute source filename.
     *
     * The filename that will be cached.
     * @see FileItem::$root_path
     * @see FileItem::$filename
     * @return string
     */
    protected function getSourceFilename() : string{
        return $this->root_path . DIRECTORY_SEPARATOR . $this->filename;
    }

    /**
     * Get the last modification of the file.
     *
     * It is the modification time in the system
     * @return DateTime
     * @throws \Exception
     * @api
     * @see filemtime()
     */
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
     * @api
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

    /**
     * Generates the cached file
     *
     * In this case only copy the file to the target.
     * @api
     * @param CacheManager $manager
     */
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
     * @api
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

    /**
     * This class does not store additional data.
     * @return array
     */
    public function getAdditionalData(): array
    {
        return [];
    }
}