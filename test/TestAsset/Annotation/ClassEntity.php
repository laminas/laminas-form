<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Name("some_name")
 * @Annotation\Attributes({"legend":"Some Fieldset"})
 * @Annotation\InputFilter("LaminasTest\Form\TestAsset\Annotation\InputFilter")
 * @Annotation\ValidationGroup({"omit", "keep"})
 */
class ClassEntity
{
    /**
     * @Annotation\Exclude()
     */
    public $omit;

    /**
     * @Annotation\Name("keeper")
     * @Annotation\Attributes({"type":"text"})
     */
    public $keep;
}
