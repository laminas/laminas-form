<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\Hydrator\ObjectPropertyHydrator;
use LaminasTest\Form\TestAsset\Annotation\UrlValidator;

/**
 * @Annotation\Name("user")
 * @Annotation\Attributes({"legend":"Register"})
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 */
#[Annotation\Name("user")]
#[Annotation\Attributes(["legend" => "Register"])]
#[Annotation\Hydrator(ObjectPropertyHydrator::class)]
class ComplexEntity
{
    /**
     * @var null|string
     * @Annotation\ErrorMessage("Invalid or missing username")
     * @Annotation\Filter("StringTrim")
     * @Annotation\Validator("NotEmpty")
     * @Annotation\Validator("StringLength", options={"min":3,"max":25})
     */
    #[Annotation\ErrorMessage("Invalid or missing username")]
    #[Annotation\Filter("StringTrim")]
    #[Annotation\Validator("NotEmpty")]
    #[Annotation\Validator("StringLength", options: ["min" => 3, "max" => 25])]
    public $username;

    /**
     * @var null|string
     * @Annotation\Attributes({"type":"password","label":"Enter your password"})
     * @Annotation\Filter("StringTrim")
     * @Annotation\Validator("StringLength", options={"min":3})
     */
    #[Annotation\Attributes(["type" => "password", "label" => "Enter your password"])]
    #[Annotation\Filter("StringTrim")]
    #[Annotation\Validator("StringLength", options: ["min" => 3])]
    public $password;

    /**
     * @var null|string
     * @Annotation\Flags({"priority":100})
     * @Annotation\Filter("StringTrim")
     * @Annotation\Validator("EmailAddress", options={"allow":15})
     * @Annotation\Attributes({"type":"email","label":"What is the best email to reach you at?"})
     */
    #[Annotation\Flags(["priority" => 100])]
    #[Annotation\Filter("StringTrim")]
    #[Annotation\Validator("EmailAddress", options: ["allow" => 15])]
    #[Annotation\Attributes(["type" => "email", "label" => "What is the best email to reach you at?"])]
    public $email;

    /**
     * @var null|string
     * @Annotation\Name("user_image")
     * @Annotation\AllowEmpty()
     * @Annotation\Required(false)
     * @Annotation\Attributes({"type":"text","label":"Provide a URL for your avatar (optional):"})
     * @Annotation\Validator("LaminasTest\Form\TestAsset\Annotation\UrlValidator")
     */
    #[Annotation\Name("user_image")]
    #[Annotation\AllowEmpty()]
    #[Annotation\Required(false)]
    #[Annotation\Attributes(["type" => "text", "label" => "Provide a URL for your avatar (optional):"])]
    #[Annotation\Validator(UrlValidator::class)]
    public $avatar;

    /**
     * @var null|string
     * @Annotation\Exclude()
     */
    #[Annotation\Exclude]
    protected $someComposedObject;
}
