<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Options({"use_as_base_fieldset":true})
 */
#[Annotation\Options(["use_as_base_fieldset" => true])]
class EntityUsingOptions
{
    /** @Annotation\Options({"label":"Username:", "label_attributes": {"class": "label"}}) */
    #[Annotation\Options(["label" => "Username:", "label_attributes" => ["class" => "label"]])]
    public $username;
}
