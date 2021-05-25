<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset\Annotation\InputFilterInput;

class EntityWithInputFilterInput
{
    /** @Annotation\Input("LaminasTest\Form\TestAsset\Annotation\InputFilterInput") */
    #[Annotation\Input(InputFilterInput::class)]
    public $input;
}
