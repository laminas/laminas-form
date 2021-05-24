<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class EntityWithTypeAsElementName
{
    /**
      * @Annotation\Required(true)
      * @Annotation\Filter({"name":"StringTrim"})
      * @Annotation\Name("type")
      */
    public $type;
}
