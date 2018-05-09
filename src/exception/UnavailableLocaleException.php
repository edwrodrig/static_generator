<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\exception;

use Exception;

/**
 * Class UnavailableLocaleException
 * @package edwrodrig\static_generator\exception
 * @api
 */
class UnavailableLocaleException extends Exception
{

    /**
     * UnavailableLocaleException constructor.
     * @param string $lang
     * @internal
     */
    public function __construct(string $lang)
    {
        parent::__construct($lang);
    }
}