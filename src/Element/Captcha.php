<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Captcha as LaminasCaptcha;
use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\Form\Exception;
use Laminas\InputFilter\InputProviderInterface;
use Traversable;

use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function sprintf;

class Captcha extends Element implements InputProviderInterface
{
    /** @var null|LaminasCaptcha\AdapterInterface */
    protected $captcha;

    /**
     * Accepted options for Captcha:
     * - captcha: a valid Laminas\Captcha\AdapterInterface
     *
     * @return $this
     */
    public function setOptions(iterable $options)
    {
        parent::setOptions($options);

        if (isset($this->options['captcha'])) {
            $this->setCaptcha($this->options['captcha']);
        }

        return $this;
    }

    /**
     * Set captcha
     *
     * @param  array|Traversable|LaminasCaptcha\AdapterInterface $captcha
     * @throws Exception\InvalidArgumentException
     * @return $this
     */
    public function setCaptcha($captcha)
    {
        if (is_array($captcha) || $captcha instanceof Traversable) {
            $captcha = LaminasCaptcha\Factory::factory($captcha);
        } elseif (! $captcha instanceof LaminasCaptcha\AdapterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either a Laminas\Captcha\AdapterInterface or specification'
                . ' to pass to Laminas\Captcha\Factory; received "%s"',
                __METHOD__,
                is_object($captcha) ? get_class($captcha) : gettype($captcha)
            ));
        }
        $this->captcha = $captcha;

        return $this;
    }

    /**
     * Retrieve captcha (if any)
     */
    public function getCaptcha(): ?LaminasCaptcha\AdapterInterface
    {
        return $this->captcha;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches the captcha as a validator.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        $spec = [
            'name'     => $this->getName(),
            'required' => true,
            'filters'  => [
                ['name' => StringTrim::class],
            ],
        ];

        // Test that we have a captcha before adding it to the spec
        $captcha = $this->getCaptcha();
        if ($captcha instanceof LaminasCaptcha\AdapterInterface) {
            $spec['validators'] = [$captcha];
        }

        return $spec;
    }
}
