<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class EntityWithTypeAsElementName
{
    /**
      * @Annotation\Required(true)
      * @Annotation\Filter("StringTrim")
      * @Annotation\Name("type")
      */
    #[Annotation\Required(true)]
    #[Annotation\Filter("StringTrim")]
    #[Annotation\Name("type")]
    public $type;
}
