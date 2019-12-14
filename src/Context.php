<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;


use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\template\Template;
use edwrodrig\static_generator\util\Logger;
use edwrodrig\static_generator\util\PageFileFactory;
use Generator;
use function setlocale;

/**
 * Class Context
 * A class to hold the context of page generation.
 * @package edwrodrig\static_generator
 * @api
 */
class Context
{

    /**
     * @see Context::setLogger()
     * @see Context::getLogger()
     * @var Logger
     */
    private Logger $logger;

    /**
     * The target root path of the generation
     *
     * All sources will be inside this root path
     * @see Context::getTargetRootPath()
     * @var string
     */
    private string $target_root_path;

    /**
     * The target web path of the generation.
     *
     * It is the base path that is used in the web side. It is different from {@see Context::$target_root_path}
     * in the way that this one is displayed when the site is deployed (if this is the site)
     * @see Context::getUrl()
     * @var string
     */
    private string $target_web_path;

    /**
     * The target domain.
     *
     * For example. http://www.edwin.cl or something. Useful to generate {@see Context::getFullUrl() full urls}
     * @var string
     */
    private string $target_web_domain;

    /**
     * The source root path of the generation
     *
     * All sources should be inside this root path
     * @see Context::getSourceRootPath()
     * @var string
     */
    private string $source_root_path;

    /**
     * Registered caches
     * @var CacheManager[]
     */
    private array $caches = [];


    /**
     * Repository object
     * @var Repository
     */
    private Repository $repository;

    /**
     * Context constructor.
     * @api
     * @param string $source_root_path {@see Context::$source_root_path}
     * @param string $target_root_path {@see Context::$target_root_path}
     */
    public function __construct(string $source_root_path, string $target_root_path) {
        $this->source_root_path = $source_root_path;
        $this->target_root_path = $target_root_path;
        $this->logger = new Logger;
    }

    /**
     * Get the current logger
     *
     * Useful to change the {@see Logger::setTarget() target}
     * @api
     * @uses Context::$logger
     * @return Logger
     */
    public function getLogger() : Logger {
        return $this->logger;
    }

    /**
     * Set a logger.
     *
     * When you don't want to use the default logger.
     * @api
     * @param Logger $logger the new logger
     * @uses Context::$logger
     * @return $this
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
        return $this;
    }

    /**
     * The source root path of the generation
     *
     * @api
     * @uses Context::$source_root_path
     * @return string
     */
    public function getSourceRootPath() : string {
        return $this->source_root_path;
    }

    /**
     * Get the target web path
     * @api
     * @uses Context::$target_web_path
     * @return string
     */
    public function getTargetWebPath() : string {
        return $this->target_web_path ?? "";
    }

    /**
     * Set the target web path.
     *
     * @api
     * @uses Context::$target_web_path
     * @param string $target_web_path
     * @return $this
     */
    public function setTargetWebPath(string $target_web_path) : Context {
        $this->target_web_path = preg_replace('/\/$/', '', trim($target_web_path));
        return $this;
    }

    /**
     * Set the target web domain
     * @uses Context::$target_web_domain
     * @see Context::getFullUrl()
     * @param string $target_web_domain
     * @return Context
     */
    public function setTargetWebDomain(string $target_web_domain) : Context {
        $this->target_web_domain = $target_web_domain;
        return $this;
    }

    /**
     * The target root path of the generation
     *
     * @api
     * @uses Context::$target_root_path
     * @return string
     */
    public function getTargetRootPath() : string {
        return $this->target_root_path;
    }

    /**
     * Get the current language of the context.
     *
     * The language must be set with {@see setlocale()}.
     *
     * Examples
     * ```
     * setlocale(LC_ALL, 'es_CL.utf-8');
     * setlocale(LC_ALL, 'en_US.utf-8');
     * ```
     * @api
     * @return string
     */
    public function getLang() : string {
        $locale = setlocale(LC_ALL, "0");
        return substr($locale,0, 2);
    }

    /**
     * Check if a locale exists.
     *
     * This function is useful when installing a project using this generator on a new machine.
     * that you're not sure if the locales needed are installed
     *
     * @api
     * @param string $lang
     * @return bool
     * @throws exception\UnavailableLocaleException
     */
    public static function checkLocale(string $lang) : bool {
        $available_langs = explode("\n", shell_exec('locale -a'));

        if (!in_array($lang, $available_langs)) {
            throw new exception\UnavailableLocaleException($lang);
        }
        return true;
    }

    /**
     * Removed the target path.
     *
     * It is useful when regenerating pages.
     * @api
     */
    public function clearTarget() {
        exec(sprintf('rm -rf %s', $this->getTargetRootPath()));
    }

    /**
     * Get a translated value.
     *
     * Translate a translatable object with the {@see Context::getLang() current lang}.
     * A translatable object is any object that has {@see ArrayAccess} implemented or an array
     * @api
     * @param mixed $translatable A translatable object.
     * @param null|string $default The defaulf string if the translation is not present.
     * @param null|string $default_message if is a string then is printed a log message when default is used.
     * @return string
     * @throws exception\NoTranslationAvailableException
     */
    public function tr($translatable, ?string $default = null, ?string $default_message = null) : string
    {
        if (isset($translatable[$this->getLang()]))
            return $translatable[$this->getLang()];
        else if (is_string($default)) {
            if ( !empty($default_message) )
                $this->getLogger()->log(sprintf("ERROR_TR[%s]", $default_message));
            return $default;
        } else {
            throw new exception\NoTranslationAvailableException($translatable, $this->getLang());
        }
    }

    /**
     * Resolve is a translation exists
     *
     * Check if the translatable has a translation in the current lang.
     * @api
     * @param $translatable
     * @return bool
     *
     */
    public function hasTr($translatable) : bool {
        return isset($translatable[$this->getLang()]);
    }

    /**
     * Register a cache.
     *
     * Cache must be registered to be {@see Template::getCache() retrieved} and {@see CacheManager::update() used} by template instances
     *
     * @api
     * @param CacheManager $cache
     * @return Context
     * @throws exception\CacheAlreadyRegisteredException
     */
    public function registerCache(CacheManager $cache) : Context {
        $web_path = $cache->getTargetWebPath();
        if ( isset($this->caches[$web_path]) ) {
            throw new exception\CacheAlreadyRegisteredException($web_path);
        } else {
            $cache->setContext($this);
            $this->caches[$web_path] = $cache;
        }
        return $this;
    }


    /**
     * Get a cache.
     *
     * Retrieve a previously {@see Context::registerCache() registered cache} by {@see CacheManager::getTargetWebPath() web path}.
     * @api
     * @param string $web_path
     * @return CacheManager
     * @throws exception\CacheDoesNotExists
     */
    public function getCache(string $web_path) : CacheManager {
        if ( isset($this->caches[$web_path]) )
            return $this->caches[$web_path];
        else {
            throw new exception\CacheDoesNotExists($web_path);
        }
    }

    /**
     * Generate the pages.
     *
     * It is just a convenience function that clears all files in the {@see Context::getTargetRootPath() target}
     * and generates all from {@see Context::getSourceRootPath() source}
     * @throws exception\InvalidTemplateClassException
     * @throws exception\InvalidTemplateMetadataException
     * @throws util\exception\IgnoredPageFileException
     * @throws exception\InvalidTemplateMetadataException
     * @api
     */
    public function generate() {
        $this->clearTarget();

        foreach ( PageFileFactory::createPages($this) as $page ) {
            $page->generate();
        }

        foreach ( $this->caches as $cache ) {
            $cache->linkToTarget();
        }
    }

    /**
     * Get the templates of the context
     *
     * Just a convenience function to call {@see PageFileFactory::createTemplates() }
     * @return Template[]|Generator
     * @throws exception\InvalidTemplateClassException
     * @throws util\exception\IgnoredPageFileException
     * @throws exception\InvalidTemplateMetadataException
     * @api
     */
    public function getTemplates() {
        yield from PageFileFactory::createTemplates($this);
    }

    /**
     * Get a url with absolute target path if needed.
     *
     * It is useful when the target web folder is not /, when you have different version of a site in different folder, for example, by languages
     * @api
     * @param string $path
     * @return string
     */
    public function getUrl(string $path) : string {
        if ( strpos($path, '/') === 0 ) {
            $target_web_path = $this->getTargetWebPath();
            if ( !empty($target_web_path) )
                return '/' . $target_web_path . $path;
        }

        return $path;
    }

    /**
     * Get the full url.
     *
     * Includes the {@see Context::$target_web_domain domain}.
     * Useful when you need a canonical URL for some resources. For example {@see OpenGraph::setImage() open graph meta tags images}
     * @param string $path
     * @return string
     * @throws exception\RelativePathCanNotBeFullException
     * @throws exception\UnregisteredWebDomainException
     */
    public function getFullUrl(string $path) : string {
        if ( !isset($this->target_web_domain) )
            throw new exception\UnregisteredWebDomainException;

        $url = $this->getUrl($path);
        if ( strpos($url, '/') !== 0 ) {

            throw new exception\RelativePathCanNotBeFullException($path);
        }
        if ( empty($this->target_web_domain) )
            return $url;
        else
            return $this->target_web_domain . $url;
    }

    /**
     * Set a repository
     *
     *
     * @param Repository $repository
     * @return Context
     */
    public function setRepository(Repository $repository) : Context {
        $this->repository = $repository;
        $this->repository->setContext($this);
        return $this;
    }

    /**
     * Return a repository object.
     * @api
     * @return Repository
     */
    public function getRepository() : Repository {
        return $this->repository;
    }
}