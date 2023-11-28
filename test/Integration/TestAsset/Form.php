<?php

declare(strict_types=1);

namespace LaminasTest\Form\Integration\TestAsset;

use Laminas\Form\Form as BaseForm;
use Laminas\Form\FormElementManager;

/** @extends BaseForm<array<string, mixed>> */
final class Form extends BaseForm
{
    /** @var null|FormElementManager */
    public $elementManagerAtInit;

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        $this->elementManagerAtInit = $this->getFormFactory()->getFormElementManager();
    }
}
