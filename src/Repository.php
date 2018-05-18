<?php
declare(strict_types=1);

namespace edwrodrig\static_generator;


/**
 * Class Repository
 *
 * A base class for repositories.
 * Example of implementation
 * ```
 * public $elements;
 *
 * public function getElements() {
 *   if ( is_null($this->elements) ) {
 *     $this->elements = getArrayElementsWithKeyIndex();
 *   }
 *   return $this->elements;
 * }
 *
 * public function getElement(string $key) : Element {
 *   return $this->getElements()[$key];
 * }
 * ```
 * @package edwrodrig\static_generator
 */
class Repository
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Set context.
     *
     * Some repositories need context information to work.
     * @see Context::setRepository() uses this function to set Context
     * @param Context $context
     * @return Repository
     */
    public function setContext(Context $context) : Repository {
        $this->context = $context;
        return $this;
    }
}