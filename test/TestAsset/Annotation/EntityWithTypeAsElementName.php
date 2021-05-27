<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class EntityWithTypeAsElementName
{
    /**
     * @var null|string
      * @Annotation\Required(true)
      * @Annotation\Filter("StringTrim")
      * @Annotation\Name("type")
      */
    #[Annotation\Required(true)]
    #[Annotation\Filter("StringTrim")]
    #[Annotation\Name("type")]
    public $type;
}
