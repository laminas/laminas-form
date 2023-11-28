<?php

declare(strict_types=1);

namespace LaminasTest\Form\StaticAnalysis\Asset;

use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

/**
 * @psalm-import-type ValidPayload from ExampleInputFilter
 * @extends Form<ValidPayload>
 */
final class ExampleForm extends Form
{
    public function init(): void
    {
        $this->add([
            'name' => 'string',
            'type' => Text::class,
        ]);
        $this->add([
            'name' => 'number',
            'type' => Number::class,
        ]);
    }
}
