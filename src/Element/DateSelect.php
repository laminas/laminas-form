<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use DateTime as PhpDateTime;
use DateTimeInterface;
use Exception;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\FormInterface;
use Laminas\Validator\Date as DateValidator;
use Laminas\Validator\ValidatorInterface;

use function array_merge;
use function is_array;
use function is_string;
use function sprintf;

class DateSelect extends MonthSelect
{
    /**
     * Select form element that contains values for day
     *
     * @var Select
     */
    protected $dayElement;

    /**
     * Constructor. Add the day select element
     *
     * @param  null|int|string $name    Optional name for the element
     * @param  iterable        $options Optional options for the element
     */
    public function __construct($name = null, iterable $options = [])
    {
        $this->dayElement = new Select('day');

        parent::__construct($name, $options);
    }

    /**
     * Accepted options for DateSelect (plus the ones from MonthSelect) :
     * - day_attributes: HTML attributes to be rendered with the day element
     *
     * @return $this
     */
    public function setOptions(iterable $options)
    {
        parent::setOptions($options);

        if (isset($this->options['day_attributes'])) {
            $this->setDayAttributes($this->options['day_attributes']);
        }

        return $this;
    }

    public function getDayElement(): Select
    {
        return $this->dayElement;
    }

    /**
     * Get both the year and month elements
     *
     * @return list<Select>
     */
    public function getElements(): array
    {
        return array_merge([$this->dayElement], parent::getElements());
    }

    /**
     * Set the day attributes
     *
     * @param array<string, scalar|null> $dayAttributes
     * @return $this
     */
    public function setDayAttributes(array $dayAttributes)
    {
        $this->dayElement->setAttributes($dayAttributes);
        return $this;
    }

    /**
     * Get the day attributes
     *
     * @return array<string, scalar|null>
     */
    public function getDayAttributes(): array
    {
        return $this->dayElement->getAttributes();
    }

    /**
     * @param  PhpDateTime|iterable|string|null|mixed $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setValue($value)
    {
        if (is_string($value)) {
            try {
                $value = new PhpDateTime($value);
            } catch (Exception) {
                throw new InvalidArgumentException('Value should be a parsable string or an instance of DateTime');
            }
        }

        if (null === $value && ! $this->shouldCreateEmptyOption()) {
            $value = new PhpDateTime();
        }

        if ($value instanceof DateTimeInterface) {
            $value = [
                'year'  => $value->format('Y'),
                'month' => $value->format('m'),
                'day'   => $value->format('d'),
            ];
        }

        if (is_array($value)) {
            $this->yearElement->setValue($value['year']);
            $this->monthElement->setValue($value['month']);
            $this->dayElement->setValue($value['day']);
        } else {
            $this->yearElement->setValue(null);
            $this->monthElement->setValue(null);
            $this->dayElement->setValue(null);
        }

        return $this;
    }

    public function getValue(): ?string
    {
        $year  = $this->getYearElement()->getValue();
        $month = $this->getMonthElement()->getValue();
        $day   = $this->getDayElement()->getValue();

        if ($this->shouldCreateEmptyOption() && null === $year && null === $month && null === $day) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     */
    public function prepareElement(FormInterface $form): void
    {
        parent::prepareElement($form);

        $name = $this->getName();
        $this->dayElement->setName($name . '[day]');
    }

    /**
     * Get validator
     */
    protected function getValidator(): ValidatorInterface
    {
        if (null === $this->validator) {
            $this->validator = new DateValidator(['format' => 'Y-m-d']);
        }

        return $this->validator;
    }

    /**
     * @inheritDoc
     */
    public function getInputSpecification(): array
    {
        $spec = [
            'required'   => false,
            'filters'    => [
                ['name' => 'DateSelect'],
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
        $this->dayElement   = clone $this->dayElement;
        $this->monthElement = clone $this->monthElement;
        $this->yearElement  = clone $this->yearElement;
    }
}
