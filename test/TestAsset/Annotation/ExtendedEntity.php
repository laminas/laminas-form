<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Name("extended")
 */
#[Annotation\Name("extended")]
class ExtendedEntity extends Entity
{
    /**
      * @Annotation\Filter({"name":"StringTrim"})
      * @Annotation\Validator({"name":"EmailAddress"})
      * @Annotation\Attributes({"type":"password","label":"Enter your email"})
      * @Annotation\Flags({"priority":-1})
      */
    #[Annotation\Filter(["name" => "StringTrim"])]
    #[Annotation\Validator(["name" => "EmailAddress"])]
    #[Annotation\Attributes(["type" => "password", "label" => "Enter your email"])]
    #[Annotation\Flags(["priority" => -1])]
    public $email;
}
