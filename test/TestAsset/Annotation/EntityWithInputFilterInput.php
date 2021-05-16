<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class EntityWithInputFilterInput
{
    /**
     * @Annotation\Input("LaminasTest\Form\TestAsset\Annotation\InputFilterInput")
     */
    #[Annotation\Input("LaminasTest\Form\TestAsset\Annotation\InputFilterInput")]
    public $input;
}
