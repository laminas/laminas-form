<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use IntlDateFormatter;
use Laminas\Form\Element\MonthSelect as MonthSelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

use function is_numeric;
use function sprintf;

class FormMonthSelect extends AbstractFormDateSelect
{
    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|self
     */
    public function __invoke(
        ?ElementInterface $element = null,
        int $dateType = IntlDateFormatter::LONG,
        ?string $locale = null
    ) {
        if (! $element) {
            return $this;
        }

        $this->setDateType($dateType);

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        return $this->render($element);
    }

    /**
     * Render a month element that is composed of two selects
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (! $element instanceof MonthSelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\MonthSelect',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $selectHelper = $this->getSelectElementHelper();
        $pattern      = $this->parsePattern($element->shouldRenderDelimiters());

        // The pattern always contains "day" part and the first separator, so we have to remove it
        unset($pattern['day']);
        unset($pattern[0]);

        $monthsOptions = $this->getMonthsOptions($pattern['month']);
        $yearOptions   = $this->getYearsOptions($element->getMinYear(), $element->getMaxYear());

        $monthElement = $element->getMonthElement()->setValueOptions($monthsOptions);
        $yearElement  = $element->getYearElement()->setValueOptions($yearOptions);

        if ($element->shouldCreateEmptyOption()) {
            $monthElement->setEmptyOption('');
            $yearElement->setEmptyOption('');
        }

        $data                    = [];
        $data[$pattern['month']] = $selectHelper->render($monthElement);
        $data[$pattern['year']]  = $selectHelper->render($yearElement);

        $markup = '';
        foreach ($pattern as $key => $value) {
            // Delimiter
            if (is_numeric($key)) {
                $markup .= $value;
            } else {
                $markup .= $data[$value];
            }
        }

        return $markup;
    }
}
