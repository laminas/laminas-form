<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use DateTime;
use IntlDateFormatter;
use Laminas\Form\Element\DateTimeSelect as DateTimeSelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

use function is_numeric;
use function preg_split;
use function rtrim;
use function sprintf;
use function str_replace;
use function stripos;
use function strpos;
use function trim;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

class FormDateTimeSelect extends AbstractFormDateSelect
{
    /**
     * Time formatter to use
     *
     * @var int
     */
    protected $timeType;

    /**
     * @throws Exception\ExtensionNotLoadedException If ext/intl is not present.
     */
    public function __construct()
    {
        parent::__construct();

        // Delaying initialization until we know ext/intl is available
        $this->timeType = IntlDateFormatter::LONG;
    }

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
        int $timeType = IntlDateFormatter::LONG,
        ?string $locale = null
    ) {
        if (! $element) {
            return $this;
        }

        $this->setDateType($dateType);
        $this->setTimeType($timeType);

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        return $this->render($element);
    }

    /**
     * Render a date element that is composed of six selects
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (! $element instanceof DateTimeSelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\DateTimeSelect',
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

        $shouldRenderDelimiters = $element->shouldRenderDelimiters();
        $selectHelper           = $this->getSelectElementHelper();
        $pattern                = $this->parsePattern($shouldRenderDelimiters);

        $daysOptions   = $this->getDaysOptions($pattern['day']);
        $monthsOptions = $this->getMonthsOptions($pattern['month']);
        $yearOptions   = $this->getYearsOptions($element->getMinYear(), $element->getMaxYear());
        $hourOptions   = $this->getHoursOptions($pattern['hour']);
        $minuteOptions = $this->getMinutesOptions($pattern['minute']);
        $secondOptions = $this->getSecondsOptions($pattern['second']);

        $dayElement    = $element->getDayElement()->setValueOptions($daysOptions);
        $monthElement  = $element->getMonthElement()->setValueOptions($monthsOptions);
        $yearElement   = $element->getYearElement()->setValueOptions($yearOptions);
        $hourElement   = $element->getHourElement()->setValueOptions($hourOptions);
        $minuteElement = $element->getMinuteElement()->setValueOptions($minuteOptions);
        $secondElement = $element->getSecondElement()->setValueOptions($secondOptions);

        if ($element->shouldCreateEmptyOption()) {
            $dayElement->setEmptyOption('');
            $yearElement->setEmptyOption('');
            $monthElement->setEmptyOption('');
            $hourElement->setEmptyOption('');
            $minuteElement->setEmptyOption('');
            $secondElement->setEmptyOption('');
        }

        $data                     = [];
        $data[$pattern['day']]    = $selectHelper->render($dayElement);
        $data[$pattern['month']]  = $selectHelper->render($monthElement);
        $data[$pattern['year']]   = $selectHelper->render($yearElement);
        $data[$pattern['hour']]   = $selectHelper->render($hourElement);
        $data[$pattern['minute']] = $selectHelper->render($minuteElement);

        if ($element->shouldShowSeconds()) {
            $data[$pattern['second']] = $selectHelper->render($secondElement);
        } else {
            unset($pattern['second']);
            if ($shouldRenderDelimiters) {
                unset($pattern[4]);
            }
        }

        $markup = '';
        foreach ($pattern as $key => $value) {
            // Delimiter
            if (is_numeric($key)) {
                $markup .= $value;
            } else {
                $markup .= $data[$value];
            }
        }

        return trim($markup);
    }

    /**
     * @return $this
     */
    public function setTimeType(int $timeType)
    {
        // The FULL format uses values that are not used
        if ($timeType === IntlDateFormatter::FULL) {
            $timeType = IntlDateFormatter::LONG;
        }

        $this->timeType = $timeType;

        return $this;
    }

    public function getTimeType(): int
    {
        return $this->timeType;
    }

    /**
     * Override to also get time part
     */
    public function getPattern(): string
    {
        if ($this->pattern === null) {
            $intl = new IntlDateFormatter($this->getLocale(), $this->dateType, $this->timeType);
            // remove time zone format character
            $pattern       = rtrim($intl->getPattern(), ' z');
            $this->pattern = $pattern;
        }

        return $this->pattern;
    }

    /**
     * Parse the pattern
     *
     * @return array
     */
    protected function parsePattern(bool $renderDelimiters = true): array
    {
        $pattern    = $this->getPattern();
        $pregResult = preg_split(
            "/([ -,.:\/]*'.*?'[ -,.:\/]*)|([ -,.:\/]+)/",
            $pattern,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $result = [];
        foreach ($pregResult as $value) {
            if (stripos($value, "'") === false && stripos($value, 'd') !== false) {
                $result['day'] = $value;
            } elseif (stripos($value, "'") === false && strpos($value, 'M') !== false) {
                $result['month'] = $value;
            } elseif (stripos($value, "'") === false && stripos($value, 'y') !== false) {
                $result['year'] = $value;
            } elseif (stripos($value, "'") === false && stripos($value, 'h') !== false) {
                $result['hour'] = $value;
            } elseif (stripos($value, "'") === false && stripos($value, 'm') !== false) {
                $result['minute'] = $value;
            } elseif (stripos($value, "'") === false && strpos($value, 's') !== false) {
                $result['second'] = $value;
            } elseif (stripos($value, "'") === false && stripos($value, 'a') !== false) {
                // ignore ante/post meridiem marker
                continue;
            } elseif ($renderDelimiters) {
                $result[] = str_replace("'", '', $value);
            }
        }

        return $result;
    }

    /**
     * Create a key => value options for days
     *
     * @param  string $pattern Pattern to use for days
     * @return array
     */
    protected function getDaysOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            'dd'
        );
        $valueFormatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            $pattern
        );
        $date           = new DateTime('1970-01-01');

        $result = [];
        for ($day = 1; $day <= 31; $day++) {
            $key          = $keyFormatter->format($date->getTimestamp());
            $value        = $valueFormatter->format($date->getTimestamp());
            $result[$key] = $value;

            $date->modify('+1 day');
        }

        return $result;
    }

    /**
     * Create a key => value options for hours
     *
     * @param  string $pattern Pattern to use for hours
     * @return array
     */
    protected function getHoursOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            'HH'
        );
        $valueFormatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            $pattern
        );
        $date           = new DateTime('1970-01-01 00:00:00');

        $result = [];
        for ($hour = 1; $hour <= 24; $hour++) {
            $key          = $keyFormatter->format($date);
            $value        = $valueFormatter->format($date);
            $result[$key] = $value;

            $date->modify('+1 hour');
        }

        return $result;
    }

    /**
     * Create a key => value options for minutes
     *
     * @param  string $pattern Pattern to use for minutes
     * @return array
     */
    protected function getMinutesOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            'mm'
        );
        $valueFormatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            $pattern
        );
        $date           = new DateTime('1970-01-01 00:00:00');

        $result = [];
        for ($min = 1; $min <= 60; $min++) {
            $key          = $keyFormatter->format($date);
            $value        = $valueFormatter->format($date);
            $result[$key] = $value;

            $date->modify('+1 minute');
        }

        return $result;
    }

    /**
     * Create a key => value options for seconds
     *
     * @param  string $pattern Pattern to use for seconds
     * @return array
     */
    protected function getSecondsOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            'ss'
        );
        $valueFormatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            $pattern
        );
        $date           = new DateTime('1970-01-01 00:00:00');

        $result = [];
        for ($sec = 1; $sec <= 60; $sec++) {
            $key          = $keyFormatter->format($date);
            $value        = $valueFormatter->format($date);
            $result[$key] = $value;

            $date->modify('+1 second');
        }

        return $result;
    }
}
