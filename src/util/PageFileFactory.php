<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\util;


use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\exception\InvalidTemplateClassException;
use edwrodrig\static_generator\exception\InvalidTemplateMetadataException;
use edwrodrig\static_generator\PageCopy;
use edwrodrig\static_generator\PageFile;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\PageScss;
use edwrodrig\static_generator\template\Template;
use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class PageFileFactory
 * An Utility class to generate page object from files and directories.
 * @api
 * @package edwrodrig\static_generator\util
 */
class PageFileFactory
{

    /**
     * Check a file name is a processable scss file.
     *
     * scss files that start with a _ are {@see PageFileFactory::isIgnore() ignored} because are intended to be included in other scss files.
     * @api
     * @param string $filename
     * @return bool
     */

    public static function isScss(string $filename) : bool {
        $filename = basename($filename);
        return preg_match('/^[^_].*\.scss$/', $filename) === 1;
    }

    /**
     * Is the file a php file.
     *
     * Just check by extension
     * @api
     * @param string $filename
     * @return bool
     */
    public static function isPhp(string $filename) : bool {
        $filename = basename($filename);
        return preg_match('/\.php$/', $filename) === 1;
    }

    /**
     * Should the file be ignored.
     *
     * It ignore vim swap files and {@see PageFileFactory::isScss()scss include files}
     * @api
     * @param string $filename
     * @return bool
     */
    public static function isIgnore(string $filename) : bool {
        $filename = basename($filename);
        if ( preg_match('/^_.*\.scss$/', $filename) === 1)
            return true;
        else if ( preg_match('/\.swp$/', $filename) === 1)
            return true;
        else if ( preg_match('/^\./', $filename) === 1)
            return true;
        else
            return false;
    }

    /**
     * Create a page generation object for a filename.
     *
     * @param string $filename
     * @param Context $context The generation context
     * @return PageFile A Class name for the generator
     * @throws InvalidTemplateClassException
     * @throws exception\IgnoredPageFileException
     * @throws InvalidTemplateMetadataException
     * @api
     */
    public static function createPage(string $filename, Context $context) : PageFile {
        if ( self::isPhp($filename) ) {
            return new PagePhp($filename, $context);
        } else if ( self::isScss($filename) ) {
            return new PageScss($filename, $context);
        } else if ( self::isIgnore($filename) ) {
            throw new exception\IgnoredPageFileException($filename);
        } else {
            return new PageCopy($filename, $context);
        }
    }

    /**
     * Create pages generation objects by files.
     *
     * It iterates an the {@see Context::setSourceRootPath() source root path} of the context and yields every  processable file as a {@see PageFile}.
     * The key of tevery element is their sub path ( the path of the file relative the {@see Context::setSourceRootPath() source root path}
     * so it's safe to {@see iterator_to_array() convert the generator to an array} using keys
     * @param Context $context
     * @return Generator|PageFile[]
     * @throws InvalidTemplateClassException
     * @throws InvalidTemplateMetadataException
     * @throws exception\IgnoredPageFileException
     * @api
     */
    public static function createPages(Context $context) {


        $iterator = new RecursiveDirectoryIterator(
            $context->getSourceRootPath(),
            FilesystemIterator::CURRENT_AS_SELF
        );

        /** @var $file RecursiveDirectoryIterator */
        foreach ( new RecursiveIteratorIterator($iterator) as $file ) {
            if ( !$file->isFile() ) continue;
            if ( self::isIgnore($file->getSubPathName()) ) continue;

            yield $file->getSubPathName() => self::createPage($file->getSubPathName(), $context);
        }
    }

    /**
     * The same as {@see PageFileFactory::createPages()} but only considering {@see PagePhp::isTemplate() templates}
     *
     * @param Context $context
     * @return Generator|Template[]
     * @throws InvalidTemplateClassException
     * @throws InvalidTemplateMetadataException
     * @throws exception\IgnoredPageFileException
     * @api
     */
    public static function createTemplates(Context $context) {

        foreach ( self::createPages($context) as $sub_path => $page ) {

            if ( $page instanceof PagePhp && $page->isTemplate() ) {
                yield $sub_path => $page->getTemplate();
            }
        }
    }
}