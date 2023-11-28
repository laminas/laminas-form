<?php

declare(strict_types=1);

namespace LaminasTest\Form\StaticAnalysis\Asset;

use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;

/**
 * @psalm-type ValidPayload = array{
 *     string: non-empty-string,
 *     number: int,
 * }
 * @extends InputFilter<ValidPayload>
 */
final class ExampleInputFilter extends InputFilter
{
    public function init(): void
    {
        $this->add([
            'name'     => 'string',
            'required' => true,
        ]);

        $this->add([
            'name'     => 'number',
            'required' => true,
            'filters'  => [
                ['name' => ToInt::class],
            ],
        ]);
    }
}
