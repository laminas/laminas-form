<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

use function is_array;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

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
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
final class Filter
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $options;

    /** @var int|null */
    protected $priority;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string|array $name
     * @param array $options
     */
    public function __construct($name, array $options = [], ?int $priority = null)
    {
        if (is_array($name)) {
            // support for legacy notation with array as first parameter
            trigger_error(sprintf(
                'Passing a single array to the constructor of %s is deprecated since 3.0.0,'
                . ' please use separate parameters.',
                static::class
            ), E_USER_DEPRECATED);

            $this->name     = $name['name'] ?? null;
            $this->options  = $name['options'] ?? $options;
            $this->priority = $name['priority'] ?? $priority;
        } else {
            $this->name     = $name;
            $this->options  = $options;
            $this->priority = $priority;
        }
    }

    /**
     * Retrieve the filter specification
     *
     * @return array
     */
    public function getFilterSpecification(): array
    {
        $inputSpec = ['name' => $this->name];
        if (! empty($this->options)) {
            $inputSpec['options'] = $this->options;
        }
        if (null !== $this->priority) {
            $inputSpec['priority'] = $this->priority;
        }

        return $inputSpec;
    }
}
