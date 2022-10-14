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
 * Validator annotation
 *
 * Expects an associative array defining the validator.
 *
 * Typically, this includes the "name" with an associated string value
 * indicating the validator name or class, and optionally an "options" key
 * with an object/associative array value of options to pass to the
 *
 *
 * This annotation may be specified multiple times; validators will be added
 * to the validator chain in the order specified.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
final class Validator
{
    /** @var string */
    private $name;

    /** @var array */
    private $options;

    /** @var bool|null */
    private $breakChainOnFailure;

    /** @var int|null */
    private $priority;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string|array $name
     */
    public function __construct($name, array $options = [], ?bool $breakChainOnFailure = null, ?int $priority = null)
    {
        if (is_array($name)) {
            // support for legacy notation with array as first parameter
            trigger_error(sprintf(
                'Passing a single array to the constructor of %s is deprecated since 3.0.0,'
                . ' please use separate parameters.',
                static::class
            ), E_USER_DEPRECATED);

            $this->name                = $name['name'] ?? null;
            $this->options             = $name['options'] ?? $options;
            $this->breakChainOnFailure = $name['break_chain_on_failure'] ?? $breakChainOnFailure;
            $this->priority            = $name['priority'] ?? $priority;
        } else {
            $this->name                = $name;
            $this->options             = $options;
            $this->breakChainOnFailure = $breakChainOnFailure;
            $this->priority            = $priority;
        }
    }

    /**
     * Retrieve the validator specification
     *
     * @return array
     */
    public function getValidatorSpecification(): array
    {
        $inputSpec = ['name' => $this->name];
        if (! empty($this->options)) {
            $inputSpec['options'] = $this->options;
        }
        if (null !== $this->breakChainOnFailure) {
            $inputSpec['break_chain_on_failure'] = $this->breakChainOnFailure;
        }
        if (null !== $this->priority) {
            $inputSpec['priority'] = $this->priority;
        }

        return $inputSpec;
    }
}
