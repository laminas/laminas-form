<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset\Annotation\InputFilterInput;

class EntityWithInputFilterInput
{
    /**
     * @var null|string
     * @Annotation\Input("LaminasTest\Form\TestAsset\Annotation\InputFilterInput")
     */
    #[Annotation\Input(InputFilterInput::class)]
    public $input;
}
