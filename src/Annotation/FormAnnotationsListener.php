<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;

/**
 * Default listeners for form annotations
 *
 * Defines and attaches a set of default listeners for form annotations
 * (which are defined on object properties). These include:
 *
 * - Attributes
 * - Flags
 * - Hydrator
 * - Object and Instance (the latter is preferred starting in 2.4)
 * - InputFilter
 * - Type
 * - ValidationGroup
 *
 * See the individual annotation classes for more details. The handlers
 * registered work with the annotation values, as well as the form
 * specification passed in the event object.
 */
final class FormAnnotationsListener extends AbstractAnnotationsListener
{
    /**
     * Attach listeners
     *
     * @inheritDoc
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleAttributesAnnotation'], $priority);
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleFlagsAnnotation'], $priority);
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleHydratorAnnotation'], $priority);
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleInputFilterAnnotation'], $priority);
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleInstanceAnnotation'], $priority);
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleOptionsAnnotation'], $priority);
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleTypeAnnotation'], $priority);
        $this->listeners[] = $events->attach('configureForm', [$this, 'handleValidationGroupAnnotation'], $priority);

        $this->listeners[] = $events->attach('discoverName', [$this, 'handleNameAnnotation'], $priority);
        $this->listeners[] = $events->attach('discoverName', [$this, 'discoverFallbackName'], $priority);
    }

    /**
     * Handle the Attributes annotation
     *
     * Sets the attributes key of the form specification.
     */
    public function handleAttributesAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof Attributes) {
            return;
        }

        $formSpec               = $e->getParam('formSpec');
        $formSpec['attributes'] = $annotation->getAttributes();
    }

    /**
     * Handle the Flags annotation
     *
     * Sets the flags key of the form specification.
     */
    public function handleFlagsAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof Flags) {
            return;
        }

        $formSpec          = $e->getParam('formSpec');
        $formSpec['flags'] = $annotation->getFlags();
    }

    /**
     * Handle the Hydrator annotation
     *
     * Sets the hydrator class to use in the form specification.
     */
    public function handleHydratorAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof Hydrator) {
            return;
        }

        $formSpec             = $e->getParam('formSpec');
        $formSpec['hydrator'] = $annotation->getHydratorSpecification();
    }

    /**
     * Handle the InputFilter annotation
     *
     * Sets the input filter class to use in the form specification.
     */
    public function handleInputFilterAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof InputFilter) {
            return;
        }

        $formSpec                 = $e->getParam('formSpec');
        $formSpec['input_filter'] = $annotation->getInputFilter();
    }

    /**
     * Handle the Instance annotations
     *
     * Sets the object to bind to the form or fieldset
     */
    public function handleInstanceAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof Instance) {
            return;
        }

        $formSpec           = $e->getParam('formSpec');
        $formSpec['object'] = $annotation->getInstance();
    }

    /**
     * Handle the Options annotation
     *
     * Sets the options key of the form specification.
     */
    public function handleOptionsAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof Options) {
            return;
        }

        $formSpec            = $e->getParam('formSpec');
        $formSpec['options'] = $annotation->getOptions();
    }

    /**
     * Handle the Type annotation
     *
     * Sets the form class to use in the form specification.
     */
    public function handleTypeAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof Type) {
            return;
        }

        $formSpec         = $e->getParam('formSpec');
        $formSpec['type'] = $annotation->getType();
    }

    /**
     * Handle the ValidationGroup annotation
     *
     * Sets the validation group to use in the form specification.
     */
    public function handleValidationGroupAnnotation(EventInterface $e): void
    {
        $annotation = $e->getParam('annotation');
        if (! $annotation instanceof ValidationGroup) {
            return;
        }

        $formSpec                     = $e->getParam('formSpec');
        $formSpec['validation_group'] = $annotation->getValidationGroup();
    }
}
