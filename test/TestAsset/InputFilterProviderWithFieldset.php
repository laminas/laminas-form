<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

/** @extends Form<array{foo: non-empty-string}> */
class InputFilterProviderWithFieldset extends Form implements InputFilterProviderInterface
{
    /** @inheritDoc */
    public function __construct(string|null $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name'    => 'foo',
            'options' => [
                'label' => 'Foo',
            ],
        ]);

        $this->add(new BasicFieldset());
    }

    /** @inheritDoc */
    public function getInputFilterSpecification()
    {
        return [
            'foo' => [
                'required' => true,
            ],
        ];
    }
}
