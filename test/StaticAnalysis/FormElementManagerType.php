<?php

declare(strict_types=1);

namespace LaminasTest\Form\StaticAnalysis;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\ElementInterface;
use Laminas\Form\FormElementManager;

final class FormElementManagerType
{
    private function __construct(private FormElementManager $manager)
    {
    }

    public function getReturnsAnElementInterfaceWhenGivenAClassString(): ElementInterface
    {
        return $this->manager->get(Checkbox::class);
    }
}
