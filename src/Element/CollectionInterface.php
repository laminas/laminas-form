<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\ElementInterface;
use Laminas\Form\FieldsetInterface;

interface CollectionInterface extends FieldsetInterface
{
    /**
     * Default template placeholder
     */
    public const DEFAULT_TEMPLATE_PLACEHOLDER = '__index__';

    /**
     * Get the initial count of target element
     */
    public function getCount(): int;

    /**
     * Get target element
     */
    public function getTargetElement(): ?ElementInterface;

    /**
     * Get allow add
     */
    public function allowAdd(): bool;

    /**
     * Returns true if elements can be removed to the form
     */
    public function allowRemove(): bool;

    /**
     * Get if the collection should create a template
     */
    public function shouldCreateTemplate(): bool;

    /**
     * Get the template placeholder
     */
    public function getTemplatePlaceholder(): string;

    /**
     * Returns true if new objects created during modify
     */
    public function createNewObjects(): bool;

    /**
     * Get a template element used for rendering purposes only
     *
     * @return null|ElementInterface|FieldsetInterface
     */
    public function getTemplateElement();
}
