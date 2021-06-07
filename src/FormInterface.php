<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\InputFilter\InputFilterInterface;

interface FormInterface extends FieldsetInterface
{
    public const BIND_ON_VALIDATE  = 0x00;
    public const BIND_MANUAL       = 0x01;
    public const VALUES_NORMALIZED = 0x11;
    public const VALUES_RAW        = 0x12;
    public const VALUES_AS_ARRAY   = 0x13;

    /**
     * Set data to validate and/or populate elements
     *
     * Typically, also passes data on to the composed input filter.
     *
     * @return $this
     */
    public function setData(iterable $data);

    /**
     * Bind an object to the element
     *
     * Allows populating the object with validated values.
     *
     * @return mixed
     */
    public function bind(object $object, int $flags = FormInterface::VALUES_NORMALIZED);

    /**
     * Whether or not to bind values to the bound object when validation succeeds
     *
     * @return $this
     */
    public function setBindOnValidate(int $bindOnValidateFlag);

    /**
     * Set input filter
     *
     * @return $this
     */
    public function setInputFilter(InputFilterInterface $inputFilter);

    /**
     * Retrieve input filter
     */
    public function getInputFilter(): InputFilterInterface;

    /**
     * Validate the form
     *
     * Typically, will proxy to the composed input filter.
     */
    public function isValid(): bool;

    /**
     * Retrieve the validated data
     *
     * By default, retrieves normalized values; pass one of the VALUES_*
     * constants to shape the behavior.
     *
     * @return array|object
     */
    public function getData(int $flag = FormInterface::VALUES_NORMALIZED);

    /**
     * Set the validation group (set of values to validate)
     *
     * Typically, proxies to the composed input filter
     *
     * @return $this
     */
    public function setValidationGroup(array $group);

    /**
     * Reset the form to validate all elements
     */
    public function setValidateAll(): void;
}
