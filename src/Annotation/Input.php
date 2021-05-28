<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Input annotation
 *
 * Use this annotation to specify a specific input class to use with an element.
 * The value should be a string indicating the fully qualified class name of the
 * input to use.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class Input
{
    /** @var string */
    protected $input;

    /**
     * Receive and process the contents of an annotation
     */
    public function __construct(string $input)
    {
        $this->input = $input;
    }

    /**
     * Retrieve the input class
     */
    public function getInput(): string
    {
        return $this->input;
    }
}
