<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use DateTime as PhpDateTime;
use DateTimeInterface;
use Exception;
use Laminas\Form\Element;
use Laminas\Form\ElementPrepareAwareInterface;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Regex as RegexValidator;
use Laminas\Validator\ValidatorInterface;

use function date;
use function is_array;
use function is_string;
use function sprintf;

class MonthSelect extends Element implements InputProviderInterface, ElementPrepareAwareInterface
{
    /**
     * Select form element that contains values for month
     *
     * @var Select
     */
    protected $monthElement;

    /**
     * Select form element that contains values for year
     *
     * @var Select
     */
    protected $yearElement;

    /**
     * Min year to use for the select (default: current year - 100)
     *
     * @var int
     */
    protected $minYear;

    /**
     * Max year to use for the select (default: current year)
     *
     * @var int
     */
    protected $maxYear;

    /**
     * If set to true, it will generate an empty option for every select (this is mainly needed by most JavaScript
     * libraries to allow to have a placeholder)
     *
     * @var bool
     */
    protected $createEmptyOption = false;

    /**
     * If set to true, view helpers will render delimiters between <select> elements, according to the
     * specified locale
     *
     * @var bool
     */
    protected $renderDelimiters = true;

    /** @var null|ValidatorInterface */
    protected $validator;

    /**
     * Constructor. Add two selects elements
     *
     * @param  null|int|string $name    Optional name for the element
     * @param  iterable        $options Optional options for the element
     */
    public function __construct($name = null, iterable $options = [])
    {
        $this->minYear = ((int) date('Y')) - 100;
        $this->maxYear = (int) date('Y');

        $this->monthElement = new Select('month');
        $this->yearElement  = new Select('year');

        parent::__construct($name, $options);
    }

    /**
     * Set element options.
     *
     * Accepted options for MonthSelect:
     *
     * - month_attributes: HTML attributes to be rendered with the month element
     * - year_attributes: HTML attributes to be rendered with the month element
     * - min_year: min year to use in the year select
     * - max_year: max year to use in the year select
     *
     * @return $this
     */
    public function setOptions(iterable $options)
    {
        parent::setOptions($options);

        if (isset($this->options['month_attributes'])) {
            $this->setMonthAttributes($this->options['month_attributes']);
        }

        if (isset($this->options['year_attributes'])) {
            $this->setYearAttributes($this->options['year_attributes']);
        }

        if (isset($this->options['min_year'])) {
            $this->setMinYear($this->options['min_year']);
        }

        if (isset($this->options['max_year'])) {
            $this->setMaxYear($this->options['max_year']);
        }

        if (isset($this->options['create_empty_option'])) {
            $this->setShouldCreateEmptyOption($this->options['create_empty_option']);
        }

        if (isset($this->options['render_delimiters'])) {
            $this->setShouldRenderDelimiters($this->options['render_delimiters']);
        }

        return $this;
    }

    public function getMonthElement(): Select
    {
        return $this->monthElement;
    }

    public function getYearElement(): Select
    {
        return $this->yearElement;
    }

    /**
     * Get both the year and month elements
     *
     * @return list<Select>
     */
    public function getElements(): array
    {
        return [$this->monthElement, $this->yearElement];
    }

    /**
     * Set the month attributes
     *
     * @param array<string, scalar|null> $monthAttributes
     * @return $this
     */
    public function setMonthAttributes(array $monthAttributes)
    {
        $this->monthElement->setAttributes($monthAttributes);
        return $this;
    }

    /**
     * Get the month attributes
     *
     * @return array<string, scalar|null>
     */
    public function getMonthAttributes(): array
    {
        return $this->monthElement->getAttributes();
    }

    /**
     * Set the year attributes
     *
     * @param array<string, scalar|null> $yearAttributes
     * @return $this
     */
    public function setYearAttributes(array $yearAttributes)
    {
        $this->yearElement->setAttributes($yearAttributes);
        return $this;
    }

    /**
     * Get the year attributes
     *
     * @return array<string, scalar|null>
     */
    public function getYearAttributes(): array
    {
        return $this->yearElement->getAttributes();
    }

    /**
     * @return $this
     */
    public function setMinYear(int $minYear)
    {
        $this->minYear = $minYear;
        return $this;
    }

    public function getMinYear(): int
    {
        return $this->minYear;
    }

    /**
     * @return $this
     */
    public function setMaxYear(int $maxYear)
    {
        $this->maxYear = $maxYear;
        return $this;
    }

    public function getMaxYear(): int
    {
        return $this->maxYear;
    }

    /**
     * @return $this
     */
    public function setShouldCreateEmptyOption(bool $createEmptyOption)
    {
        $this->createEmptyOption = $createEmptyOption;
        return $this;
    }

    public function shouldCreateEmptyOption(): bool
    {
        return $this->createEmptyOption;
    }

    /**
     * @return $this
     */
    public function setShouldRenderDelimiters(bool $renderDelimiters)
    {
        $this->renderDelimiters = $renderDelimiters;
        return $this;
    }

    public function shouldRenderDelimiters(): bool
    {
        return $this->renderDelimiters;
    }

    /**
     * @param  PhpDateTime|iterable|string|null|mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        if (is_string($value)) {
            try {
                $value = new PhpDateTime($value);
            } catch (Exception $exception) {
                throw new InvalidArgumentException(
                    'Value should be a parsable string or an instance of \DateTime',
                    0,
                    $exception
                );
            }
        }

        if (null === $value && ! $this->shouldCreateEmptyOption()) {
            $value = new PhpDateTime();
        }

        if ($value instanceof DateTimeInterface) {
            $value = [
                'year'  => $value->format('Y'),
                'month' => $value->format('m'),
            ];
        }

        if (is_array($value)) {
            $this->yearElement->setValue($value['year']);
            $this->monthElement->setValue($value['month']);
        } else {
            $this->yearElement->setValue(null);
            $this->monthElement->setValue(null);
        }

        return $this;
    }

    public function getValue(): ?string
    {
        $year  = $this->getYearElement()->getValue();
        $month = $this->getMonthElement()->getValue();

        if ($this->shouldCreateEmptyOption() && null === $year && null === $month) {
            return null;
        }

        return sprintf('%04d-%02d', $year, $month);
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     */
    public function prepareElement(FormInterface $form): void
    {
        $name = $this->getName();
        $this->monthElement->setName($name . '[month]');
        $this->yearElement->setName($name . '[year]');
    }

    /**
     * Get validator
     */
    protected function getValidator(): ValidatorInterface
    {
        return new RegexValidator('/^[0-9]{4}\-(0?[1-9]|1[012])$/');
    }

    /**
     * @inheritDoc
     */
    public function getInputSpecification(): array
    {
        $spec = [
            'required'   => false,
            'filters'    => [
                ['name' => 'MonthSelect'],
            ],
            'validators' => [
                $this->getValidator(),
            ],
        ];

        $name = $this->getName();
        if ($name !== null) {
            $spec['name'] = $name;
        }

        return $spec;
    }

    /**
     * Clone the element (this is needed by Collection element, as it needs different copies of the elements)
     */
    public function __clone()
    {
        $this->monthElement = clone $this->monthElement;
        $this->yearElement  = clone $this->yearElement;
    }
}
