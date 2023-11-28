<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;
use LaminasTest\Form\TestAsset\Entity\Orphan;

class OrphansFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @inheritDoc
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this
            ->setHydrator(new ArraySerializableHydrator())
            ->setObject(new Orphan());

        $this->add([
            'name'    => 'name',
            'options' => ['label' => 'Name field'],
        ]);
    }

    /** @inheritDoc */
    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required'   => false,
                'filters'    => [],
                'validators' => [],
            ],
        ];
    }
}
