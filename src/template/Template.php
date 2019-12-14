<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\template;


use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\exception\CacheDoesNotExists;
use edwrodrig\static_generator\exception\NoTranslationAvailableException;
use edwrodrig\static_generator\exception\RelativePathCanNotBeFullException;
use edwrodrig\static_generator\exception\UnregisteredWebDomainException;
use edwrodrig\static_generator\html\AAttributes;
use edwrodrig\static_generator\html\Attributes;
use edwrodrig\static_generator\html\ImgAttributes;
use edwrodrig\static_generator\PagePhp;
use edwrodrig\static_generator\Repository;
use edwrodrig\static_generator\util\Logger;
use Exception;

/**
 * Class Template
 *
 * Base class to generate templates.
 * @package edwrodrig\static_generator\template
 * @api
 */
class Template
{
    /**
     * The Page object of the file. Should always be a {@see PagePhp}
     * @var PagePhp
     */
    protected PagePhp $page_info;

    /**
     * Template constructor.
     * @api
     * @param PagePhp $page_info
     */
    public function __construct(PagePhp $page_info) {
        $this->page_info = $page_info;
    }

    /**
     * Print the content of the template.
     *
     * By default the content is processes as a php file.
     *
     * This method should be overriden in derived classes
     *
     * @see TemplateHtmlBasic::print() an example
     * @api
     */
    public function print() {
        /** @noinspection PhpIncludeInspection */
        include $this->page_info->getSourceAbsolutePath();
    }

    /**
     * Get the template type.
     *
     * This function is useful when you want to classify by types
     * @api
     * @return string
     */
    public function getTemplateType() : string {
        return 'base';
    }

    /**
     * Get an unique id for the template.
     *
     * Useful when you want to build a associative array with templates.
     * @api
     * @return string
     */
    public function getId() : string {
        return basename($this->page_info->getTargetRelativePath());
    }

    /**
     * Get the data.
     *
     * Get the data{@see PagePhp::getData() data} in the {@see Template::getInfo() php file}
     *
     * @api
     * @return array
     */
    public function getData() : array {
        return $this->page_info->getData();
    }

    /**
     * Get a url with absolute target path if needed.
     *
     * It is useful when the target web folder is not /, when you have different version of a site in different folder, for example, by languages
     * @api
     * @uses Context::getUrl()
     * @see Template::getFullUrl() for full url path
     * @param string $path
     * @return string
     */
    public function url(string $path) : string {
        return $this->page_info->getContext()->getUrl($path);

    }

    /**
     * Get a full url.
     *
     * Useful when you need a url with the domain of the site
     * @uses Context::getFullUrl() More important information
     * @param string $path
     * @return string
     * @throws RelativePathCanNotBeFullException
     * @throws UnregisteredWebDomainException
     */
    public function fullUrl(string $path) : string {
        return $this->page_info->getContext()->getFullUrl($path);
    }

    /**
     * Get the url of the current page.
     *
     * Useful to determine if you're in a current page.
     * ```
     * $this->currentUrl() == '/index.html'
     * ```
     * @api
     * @return string
     */
    public function currentUrl() : string
    {
        return $this->url('/' . $this->page_info->getTargetRelativePath());
    }

    /**
     * Get the current lang.
     *
     * Useful when you want to get the language inside the body of the template
     * @api
     * @uses Context::getLang() Is the function finally called.
     * @return string
     */
    public function getLang() : string {
        return $this->page_info->getContext()->getLang();
    }

    /**
     * Translate a string
     *
     * Useful when you want to translate inside the body of the template
     * @api
     * @uses Context::tr() Is the function finally called.
     * @param $translatable
     * @param null|string $default
     * @param null|string $default_error
     * @return string
     * @throws NoTranslationAvailableException
     */
    public function tr($translatable, ?string $default = null, ?string $default_error = null) : string {
       return $this->page_info->getContext()->tr($translatable, $default, $default_error);
    }

    /**
     * Has translation
     *
     * @api
     * @uses Context::hasTr() Is the function finally called
     * @param $translatable
     * @return bool
     */
    public function hasTr($translatable) : bool {
        return $this->page_info->getContext()->hasTr($translatable);
    }

    /**
     * Returns the current logger.
     *
     * Useful when you want to log messages inside the body of the template
     * @api
     * @return Logger
     */
    public function getLogger() : Logger {
       return $this->page_info->getLogger();
    }

    /**
     * Get the page info.
     *
     * Is the page object that contains the generation of this template
     * @api
     * @return PagePhp
     */
    public function getPageInfo() : PagePhp {
        return $this->page_info;
    }

    /**
     * Get a context cache.
     *
     * Retrieve a cache object from {@see Context::getCache() context} by {@see CacheManager::getTargetWebPath() web_path}.
     * This is useful when you want to {@see FileItem cache files}
     * ```
     * $this->getCache('some_cache')->update(new FileItem('/cacheables', 'file.pdf));
     * ```
     *
     * @api
     * @param string $web_path
     * @return CacheManager
     * @throws CacheDoesNotExists
     */
    public function getCache(string $web_path) : CacheManager {
        return $this->page_info->getCache($web_path);
    }

    /**
     * Generate a page from function.
     *
     * just call {@see PagePhp::generateFromFunction()}
     * @param string $relative_path
     * @param callable $function
     * @return string
     * @throws Exception
     * @throws \Throwable
     */
    public function generateFromFunction(string $relative_path, callable $function) : string {
        return $this->page_info->generateFromFunction($relative_path, $function);
    }

    /**
     * Get the current context repository.
     *
     * @return Repository
     */
    public function getRepository() : Repository {
        return $this->page_info->getContext()->getRepository();
    }

    /**
     * Attributes of <a> tag
     * @param array $attributes
     * @return AAttributes
     */
    public function attrA(array $attributes) : AAttributes {
        return AAttributes::create($attributes);
    }

    /**
     * Attributes of <img> tag
     * @param array $attributes
     * @return ImgAttributes
     */
    public function attrImg(array $attributes) : ImgAttributes {
        return ImgAttributes::create($attributes);
    }

    /**
     * Attributes of tag
     * @param array $attributes
     * @return Attributes
     */
    public function attr(array $attributes) : Attributes {
        return Attributes::create($attributes);
    }

}