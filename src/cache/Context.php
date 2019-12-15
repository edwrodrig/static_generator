<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\cache;

use edwrodrig\static_generator\util\Logger;

class Context implements \edwrodrig\file_cache\Context
{

    private \edwrodrig\static_generator\Context $context;

    private Logger $logger;

    public function __construct(\edwrodrig\static_generator\Context $context) {
        $this->context = $context;
        $this->logger = $context->getLogger();
    }

    public function logBegin(string $message)
    {
        $this->logger->begin($message);
    }

    public function logEnd(string $message)
    {
        $this->logger->end($message, false);
    }

    /**
     * @inheritDoc
     */
    public function getUrl(string $path): string
    {
        return $this->context->getUrl($path);
    }
}