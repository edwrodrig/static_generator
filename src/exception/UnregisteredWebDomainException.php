<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\exception;

use Exception;


/**
 * Class UnregisteredWebDomainException
 *
 * This exception is launch when there is no register web domain in a context.
 * To solve this problen you need to provide a domain in Context using {@see Context::setTargetWebDomain()}
 * @package edwrodrig\static_generator\exception
 */
class UnregisteredWebDomainException extends Exception
{
}