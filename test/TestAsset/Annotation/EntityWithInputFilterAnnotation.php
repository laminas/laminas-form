<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\InputFilter\InputFilter;

/**
 * @Annotation\InputFilter({"type":"Laminas\InputFilter\InputFilter"})
 */
#[Annotation\InputFilter(["type" => InputFilter::class])]
class EntityWithInputFilterAnnotation
{
    /**
     * @var null|string
     * @Annotation\ErrorMessage("Invalid or missing username")
     * @Annotation\Required(true)
     * @Annotation\Filter("StringTrim")
     * @Annotation\Validator("NotEmpty")
     * @Annotation\Validator("StringLength", options={"min":3,"max":25})
     */
    #[Annotation\ErrorMessage("Invalid or missing username")]
    #[Annotation\Required(true)]
    #[Annotation\Filter("StringTrim")]
    #[Annotation\Validator("NotEmpty")]
    #[Annotation\Validator("StringLength", options: ["min" => 3, "max" => 25])]
    public $username;

    /**
     * @var null|string
     * @Annotation\Filter("StringTrim")
     * @Annotation\Validator("EmailAddress")
     * @Annotation\Attributes({"type":"password","label":"Enter your password"})
     */
    #[Annotation\Filter("StringTrim")]
    #[Annotation\Validator("EmailAddress")]
    #[Annotation\Attributes(["type" => "password", "label" => "Enter your password"])]
    public $password;
}
