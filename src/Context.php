<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;


use edwrodrig\static_generator\cache\CacheManager;
use edwrodrig\static_generator\util\Logger;
use edwrodrig\static_generator\util\PageFileFactory;

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
    private $logger;

    /**
     * The target root path of the generation
     *
     * All sources will be inside this root path
     * @see Context::getTargetRootPath()
     * @var string
     */
    private $target_root_path;

    /**
     * The target web path of the generation.
     *
     * It is the base path that is used in the web side. It is different from {@see Context::$target_root_path}
     * in the way that this one is displayed when the site is deployed (if this is the site)
     * @see Context::url()
     * @var string
     */
    private $target_web_path = "";

    /**
     * The source root path of the generation
     *
     * All sources should be inside this root path
     * @see Context::getSourceRootPath()
     * @var Logger
     */
    private $source_root_path;

    /**
     * Registered caches
     * @var CacheManager[]
     */
    private $caches = [];

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
        return $this->target_web_path;
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
        $locale = \setlocale(LC_ALL, "0");
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
            /** @noinspection PhpInternalEntityUsedInspection */
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
     * @return string
     * @throws exception\NoTranslationAvailableException
     */
    public function tr($translatable, ?string $default = null) : string
    {
        if (isset($translatable[$this->getLang()]))
            return $translatable[$this->getLang()];
        else if (is_string($default)) {
            return $default;
        } else {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\NoTranslationAvailableException($translatable, $this->getLang());
        }
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
            /** @noinspection PhpInternalEntityUsedInspection */
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
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\CacheDoesNotExists($web_path);
        }
    }

    /**
     * Generate the pages.
     *
     * It is just a convenience function that clears all files in the {@see Context::getTargetRootPath() target}
     * and generates all from {@see Context::getSourceRootPath() source}
     * @api
     * @throws exception\InvalidTemplateClassException
     * @throws util\exception\IgnoredPageFileException
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
}