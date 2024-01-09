<?php

declare(strict_types=1);

namespace LaminasTest\Form\StaticAnalysis;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\ElementInterface;
use Laminas\Form\FormElementManager;
use LaminasTest\Form\TestAsset\NewProductForm;

final class FormElementManagerType
{
    private function __construct(private FormElementManager $manager)
    {
    }

    public function getReturnsAnElementInterfaceWhenGivenAClassString(): ElementInterface
    {
        return $this->manager->get(Checkbox::class);
    }

    public function getReturnsMixedWhenGivenAnAlias(): mixed
    {
        return $this->manager->get('foo');
    }

    public function getReturnsObjectOfClassWhenGivenFQCN(): NewProductForm
    {
        return $this->manager->get(NewProductForm::class);
    }
}
