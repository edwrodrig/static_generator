<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 08-05-18
 * Time: 11:10
 */

namespace edwrodrig\static_generator;


use edwrodrig\static_generator\util\Logger;

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
     * The source root path of the generation
     *
     * All sources should be inside this root path
     * @see Context::getSourceRootPath()
     * @var Logger
     */
    private $source_root_path;

    /**
     * Context constructor.
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
     * @uses Context::$source_root_path
     * @return string
     */
    public function getSourceRootPath() : string {
        return $this->source_root_path;
    }

    /**
     * The target root path of the generation
     *
     * @uses Context::$target_root_path
     * @return string
     */
    public function getTargetRootPath() : string {
        return $this->target_root_path;
    }


}