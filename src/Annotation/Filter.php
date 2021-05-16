<?php

namespace Laminas\Form\Annotation;

use Attribute;

/**
 * Filter annotation
 *
 * Expects an associative array defining the filter.  Typically, this includes
 * the "name" with an associated string value indicating the filter name or
 * class, and optionally an "options" key with an object/associative array value
 * of options to pass to the filter constructor.
 *
 * This annotation may be specified multiple times; filters will be added
 * to the filter chain in the order specified.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute(Attribute::IS_REPEATABLE | ATTRIBUTE::TARGET_ALL)]
class Filter
{
    /**
     * @var array
     */
    protected $filter;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $filter
     */
    public function __construct(array $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Retrieve the filter specification
     *
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }
}
