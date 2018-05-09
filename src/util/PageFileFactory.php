<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\util;


use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\PageCopy;
use edwrodrig\static_generator\PageFile;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\PageScss;

class PageFileFactory
{

    public static function isScss(string $filename) : bool {
        $filename = basename($filename);
        return preg_match('/^[^_].*\.scss$/', $filename) === 1;
    }

    public static function isPhp(string $filename) : bool {
        $filename = basename($filename);
        return preg_match('/\.php$/', $filename) === 1;
    }

    public static function isIgnore(string $filename) : bool {
        $filename = basename($filename);
        if ( preg_match('/^_.*\.scss$/', $filename) === 1)
            return true;
        else if ( preg_match('/\.swp$/', $filename) === 1)
            return true;
        else
            return false;
    }

    /**
     *
     * @param string $filename
     * @param Context $context
     * @return PageFile A Class name for the generator
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws exception\IgnoredPageFileException
     */
    public static function createPage(string $filename, Context $context) : PageFile {
        if ( self::isPhp($filename) ) {
            return new PagePhp($filename, $context);
        } else if ( self::isScss($filename) ) {
            return new PageScss($filename, $context);
        } else if ( self::isIgnore($filename) ) {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\IgnoredPageFileException($filename);
        } else {
            return new PageCopy($filename, $context);
        }
    }

    /**
     * Iterate files.
     * If this file is a single file then yields itself.
     * If this file is a directory then yields every nested file, if inside there are directories then applies this function recursively.
     * @param Context $context
     * @return \Generator|\edwrodrig\static_generator\PageFile[]
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws exception\IgnoredPageFileException
     */
    public static function createPages(Context $context) {

        /** @var $file \SplFileInfo */
        $iterator = new \RecursiveDirectoryIterator($context->getSourceRootPath(), \FilesystemIterator::CURRENT_AS_SELF);

        foreach ( new \RecursiveIteratorIterator($iterator) as $file ) {
            if ( !$file->isFile() ) continue;
            if ( self::isIgnore($file->getSubPathName()) ) continue;

            yield self::createPage($file->getSubPathName(), $context);
        }
    }

    /**
     * @param Context $context
     * @return \Generator|\edwrodrig\static_generator\PagePhp[]
     * @throws \edwrodrig\static_generator\exception\InvalidTemplateClassException
     * @throws exception\IgnoredPageFileException
     */
    public static function createTemplates(Context $context) {

        foreach ( self::createPages($context) as $page ) {

            if ( $page instanceof PagePhp && $page->isTemplate() ) {
                yield $page;
            }
        }
    }
}