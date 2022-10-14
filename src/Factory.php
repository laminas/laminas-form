<?php

declare(strict_types=1);

namespace Laminas\Form;

use ArrayAccess;
use Laminas\Hydrator;
use Laminas\InputFilter\Factory as InputFilterFactory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function assert;
use function class_exists;
use function gettype;
use function is_array;
use function is_iterable;
use function is_object;
use function is_string;
use function method_exists;
use function sprintf;

class Factory
{
    /** @var null|InputFilterFactory */
    protected $inputFilterFactory;

    /** @var null|FormElementManager */
    protected $formElementManager;

    public function __construct(
        ?FormElementManager $formElementManager = null,
        ?InputFilterFactory $inputFilterFactory = null
    ) {
        if ($formElementManager) {
            $this->setFormElementManager($formElementManager);
        }

        if ($inputFilterFactory) {
            $this->setInputFilterFactory($inputFilterFactory);
        }
    }

    /**
     * Set input filter factory to use when creating forms
     *
     * @return $this
     */
    public function setInputFilterFactory(InputFilterFactory $inputFilterFactory)
    {
        $this->inputFilterFactory = $inputFilterFactory;
        return $this;
    }

    /**
     * Get current input filter factory
     *
     * If none provided, uses an unconfigured instance.
     */
    public function getInputFilterFactory(): InputFilterFactory
    {
        if (null === $this->inputFilterFactory) {
            $this->setInputFilterFactory(new InputFilterFactory());
            assert(null !== $this->inputFilterFactory);
        }
        return $this->inputFilterFactory;
    }

    /**
     * Set the form element manager
     *
     * @return $this
     */
    public function setFormElementManager(FormElementManager $formElementManager)
    {
        $this->formElementManager = $formElementManager;
        return $this;
    }

    /**
     * Get form element manager
     */
    public function getFormElementManager(): FormElementManager
    {
        if ($this->formElementManager === null) {
            $this->setFormElementManager(new FormElementManager(new ServiceManager()));
            assert(null !== $this->formElementManager);
        }

        return $this->formElementManager;
    }

    /**
     * Create an element, fieldset, or form
     *
     * Introspects the 'type' key of the provided $spec, and determines what
     * type is being requested; if none is provided, assumes the spec
     * represents simply an element.
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @throws Exception\DomainException
     */
    public function create($spec): ElementInterface
    {
        $spec = $this->validateSpecification($spec, __METHOD__);
        $type = $spec['type'] ?? Element::class;

        $element = $this->getFormElementManager()->get($type);

        if ($element instanceof FormInterface) {
            return $this->configureForm($element, $spec);
        }

        if ($element instanceof FieldsetInterface) {
            return $this->configureFieldset($element, $spec);
        }

        if ($element instanceof ElementInterface) {
            return $this->configureElement($element, $spec);
        }

        throw new Exception\DomainException(sprintf(
            '%s expects the $spec["type"] to implement one of %s, %s, or %s; received %s',
            __METHOD__,
            ElementInterface::class,
            FieldsetInterface::class,
            FormInterface::class,
            $type
        ));
    }

    /**
     * Create an element
     */
    public function createElement(array $spec): ElementInterface
    {
        if (! isset($spec['type'])) {
            $spec['type'] = Element::class;
        }

        return $this->create($spec);
    }

    /**
     * Create a fieldset
     */
    public function createFieldset(array $spec): FieldsetInterface
    {
        if (! isset($spec['type'])) {
            $spec['type'] = Fieldset::class;
        }

        return $this->create($spec);
    }

    /**
     * Create a form
     */
    public function createForm(array $spec): FormInterface
    {
        if (! isset($spec['type'])) {
            $spec['type'] = Form::class;
        }

        return $this->create($spec);
    }

    /**
     * Configure an element based on the provided specification
     *
     * Specification can contain any of the following:
     * - type: the Element class to use; defaults to \Laminas\Form\Element
     * - name: what name to provide the element, if any
     * - options: an array or Traversable object of element options
     * - attributes: an array or Traversable object of element attributes to assign
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @throws Exception\DomainException
     */
    public function configureElement(ElementInterface $element, $spec): ElementInterface
    {
        $spec = $this->validateSpecification($spec, __METHOD__);

        $name       = $spec['name'] ?? null;
        $options    = $spec['options'] ?? null;
        $attributes = $spec['attributes'] ?? null;

        if ($name !== null && $name !== '') {
            $element->setName($name);
        }

        if (is_iterable($options)) {
            $element->setOptions($options);
        }

        if (is_iterable($attributes)) {
            $element->setAttributes($attributes);
        }

        return $element;
    }

    /**
     * Configure a fieldset based on the provided specification
     *
     * Specification can contain any of the following:
     * - type: the Fieldset class to use; defaults to \Laminas\Form\Fieldset
     * - name: what name to provide the fieldset, if any
     * - options: an array, Traversable, or ArrayAccess object of element options
     * - attributes: an array, Traversable, or ArrayAccess object of element
     *   attributes to assign
     * - elements: an array or Traversable object where each entry is an array
     *   or ArrayAccess object containing the keys:
     *   - flags: (optional) array of flags to pass to FieldsetInterface::add()
     *   - spec: the actual element specification, per {@link configureElement()}
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @throws Exception\DomainException
     */
    public function configureFieldset(FieldsetInterface $fieldset, $spec): FieldsetInterface
    {
        $spec     = $this->validateSpecification($spec, __METHOD__);
        $fieldset = $this->configureElement($fieldset, $spec);
        assert($fieldset instanceof FieldsetInterface);

        if (isset($spec['object'])) {
            $this->prepareAndInjectObject($spec['object'], $fieldset, __METHOD__);
        }

        if (isset($spec['hydrator'])) {
            $this->prepareAndInjectHydrator($spec['hydrator'], $fieldset, __METHOD__);
        }

        if (isset($spec['elements'])) {
            $this->prepareAndInjectElements($spec['elements'], $fieldset, __METHOD__);
        }

        if (isset($spec['fieldsets'])) {
            $this->prepareAndInjectFieldsets($spec['fieldsets'], $fieldset, __METHOD__);
        }

        $factory = $spec['factory'] ?? $this;
        $this->prepareAndInjectFactory($factory, $fieldset, __METHOD__);

        return $fieldset;
    }

    /**
     * Configure a form based on the provided specification
     *
     * Specification follows that of {@link configureFieldset()}, and adds the
     * following keys:
     *
     * - input_filter: input filter instance, named input filter class, or
     *   array specification for the input filter factory
     * - hydrator: hydrator instance or named hydrator class
     *
     * @param  array|Traversable|ArrayAccess  $spec
     */
    public function configureForm(FormInterface $form, $spec): FormInterface
    {
        $spec = $this->validateSpecification($spec, __METHOD__);
        $form = $this->configureFieldset($form, $spec);
        assert($form instanceof FormInterface);

        if (isset($spec['input_filter'])) {
            $this->prepareAndInjectInputFilter($spec['input_filter'], $form, __METHOD__);
        }

        if (isset($spec['validation_group'])) {
            $form->setValidationGroup($spec['validation_group']);
        }

        return $form;
    }

    /**
     * Validate a provided specification
     *
     * Ensures we have an array, Traversable, or ArrayAccess object, and returns it.
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @param  string $method Method invoking the validator
     * @return array|ArrayAccess
     * @throws Exception\InvalidArgumentException For invalid $spec.
     */
    protected function validateSpecification($spec, string $method)
    {
        if (is_array($spec)) {
            return $spec;
        }

        if ($spec instanceof Traversable) {
            $spec = ArrayUtils::iteratorToArray($spec);
            return $spec;
        }

        if (! $spec instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array, or object implementing Traversable or ArrayAccess; received "%s"',
                $method,
                is_object($spec) ? $spec::class : gettype($spec)
            ));
        }

        return $spec;
    }

    /**
     * Takes a list of element specifications, creates the elements, and injects them into the provided fieldset
     *
     * @param  array|Traversable|ArrayAccess $elements
     * @param  string $method Method invoking this one (for exception messages)
     */
    protected function prepareAndInjectElements($elements, FieldsetInterface $fieldset, string $method): void
    {
        $elements = $this->validateSpecification($elements, $method);

        foreach ($elements as $elementSpecification) {
            if (null === $elementSpecification) {
                continue;
            }

            $flags = $elementSpecification['flags'] ?? [];
            $spec  = $elementSpecification['spec'] ?? [];

            if (! isset($spec['type'])) {
                $spec['type'] = Element::class;
            }

            $element = $this->create($spec);
            $fieldset->add($element, $flags);
        }
    }

    /**
     * Takes a list of fieldset specifications, creates the fieldsets, and injects them into the master fieldset
     *
     * @param  array|Traversable|ArrayAccess $fieldsets
     * @param  string $method Method invoking this one (for exception messages)
     */
    public function prepareAndInjectFieldsets($fieldsets, FieldsetInterface $masterFieldset, string $method): void
    {
        $fieldsets = $this->validateSpecification($fieldsets, $method);

        foreach ($fieldsets as $fieldsetSpecification) {
            $flags = $fieldsetSpecification['flags'] ?? [];
            $spec  = $fieldsetSpecification['spec'] ?? [];

            $fieldset = $this->createFieldset($spec);
            $masterFieldset->add($fieldset, $flags);
        }
    }

    /**
     * Prepare and inject an object
     *
     * Takes a string indicating a class name, instantiates the class
     * by that name, and injects the class instance as the bound object.
     *
     * @throws Exception\DomainException
     */
    protected function prepareAndInjectObject(string $objectName, FieldsetInterface $fieldset, string $method): void
    {
        if (! class_exists($objectName)) {
            throw new Exception\DomainException(sprintf(
                '%s expects string class name to be a valid class name; received "%s"',
                $method,
                $objectName
            ));
        }

        $fieldset->setObject(new $objectName());
    }

    /**
     * Prepare and inject a named hydrator
     *
     * Takes a string indicating a hydrator class name (or a concrete instance), try first to instantiates the class
     * by pulling it from service manager, and injects the hydrator instance into the form.
     *
     * @param  string|array|Hydrator\HydratorInterface $hydratorOrName
     * @throws Exception\DomainException If $hydratorOrName is not a string, does not resolve to a known class, or
     *                                   the class does not implement Hydrator\HydratorInterface.
     */
    protected function prepareAndInjectHydrator($hydratorOrName, FieldsetInterface $fieldset, string $method): void
    {
        if ($hydratorOrName instanceof Hydrator\HydratorInterface) {
            $fieldset->setHydrator($hydratorOrName);
            return;
        }

        if (is_array($hydratorOrName)) {
            if (! isset($hydratorOrName['type'])) {
                throw new Exception\DomainException(sprintf(
                    '%s expects array specification to have a type value',
                    $method
                ));
            }
            $hydratorOptions = $hydratorOrName['options'] ?? [];
            $hydratorOrName  = $hydratorOrName['type'];
        } else {
            $hydratorOptions = [];
        }

        if (is_string($hydratorOrName)) {
            $hydrator = $this->getFormElementManager()->getHydratorFromName($hydratorOrName);
        }

        if (! isset($hydrator) || ! $hydrator instanceof Hydrator\HydratorInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects a valid implementation of Laminas\Hydrator\HydratorInterface; received "%s"',
                $method,
                $hydratorOrName
            ));
        }

        if (! empty($hydratorOptions) && $hydrator instanceof Hydrator\HydratorOptionsInterface) {
            $hydrator->setOptions($hydratorOptions);
        }

        $fieldset->setHydrator($hydrator);
    }

    /**
     * Prepare and inject a named factory
     *
     * Takes a string indicating a factory class name (or a concrete instance), try first to instantiates the class
     * by pulling it from service manager, and injects the factory instance into the fieldset.
     *
     * @param  string|array|Factory      $factoryOrName
     * @throws Exception\DomainException If $factoryOrName is not a string, does not resolve to a known class, or
     *                                   the class does not extend Form\Factory.
     */
    protected function prepareAndInjectFactory($factoryOrName, FieldsetInterface $fieldset, string $method): void
    {
        if (is_array($factoryOrName)) {
            if (! isset($factoryOrName['type'])) {
                throw new Exception\DomainException(sprintf(
                    '%s expects array specification to have a type value',
                    $method
                ));
            }
            $factoryOrName = $factoryOrName['type'];
        }

        if (is_string($factoryOrName)) {
            $factoryOrName = $this->getFormElementManager()->getFactoryFromName($factoryOrName);
        }

        if (! $factoryOrName instanceof Factory) {
            throw new Exception\DomainException(sprintf(
                '%s expects a valid extension of Laminas\Form\Factory; received "%s"',
                $method,
                $factoryOrName
            ));
        }

        $fieldset->setFormFactory($factoryOrName);
    }

    /**
     * Prepare an input filter instance and inject in the provided form
     *
     * If the input filter specified is a string, assumes it is a class name,
     * and attempts to instantiate it. If the class does not exist, or does
     * not extend InputFilterInterface, an exception is raised.
     *
     * Otherwise, $spec is passed on to the attached InputFilter Factory
     * instance in order to create the input filter.
     *
     * @param  string|array|Traversable $spec
     * @throws Exception\DomainException For unknown InputFilter class or invalid InputFilter instance.
     */
    protected function prepareAndInjectInputFilter($spec, FormInterface $form, string $method): void
    {
        if ($spec instanceof InputFilterInterface) {
            $form->setInputFilter($spec);
            return;
        }

        if (is_string($spec)) {
            if (! class_exists($spec)) {
                throw new Exception\DomainException(sprintf(
                    '%s expects string input filter names to be valid class names; received "%s"',
                    $method,
                    $spec
                ));
            }
            $filter = new $spec();
            if (! $filter instanceof InputFilterInterface) {
                throw new Exception\DomainException(sprintf(
                    '%s expects a valid implementation of Laminas\InputFilter\InputFilterInterface; received "%s"',
                    $method,
                    $spec
                ));
            }
            $form->setInputFilter($filter);
            return;
        }

        $factory = $this->getInputFilterFactory();
        $filter  = $factory->createInputFilter($spec);
        if (method_exists($filter, 'setFactory')) {
            $filter->setFactory($factory);
        }
        $form->setInputFilter($filter);
    }
}
