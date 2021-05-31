<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use DateTime;
use IntlDateFormatter;
use Laminas\Form\Exception;
use Locale;

use function assert;
use function extension_loaded;
use function method_exists;
use function preg_split;
use function sprintf;
use function str_replace;
use function stripos;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

abstract class AbstractFormDateSelect extends AbstractHelper
{
    /**
     * FormSelect helper
     *
     * @var null|FormSelect
     */
    protected $selectHelper;

    /**
     * Date formatter to use
     *
     * @var int
     */
    protected $dateType;

    /**
     * Pattern to use for Date rendering
     *
     * @var null|string
     */
    protected $pattern;

    /**
     * Locale to use
     *
     * @var null|string
     */
    protected $locale;

    /**
     * @throws Exception\ExtensionNotLoadedException If ext/intl is not present.
     */
    public function __construct()
    {
        if (! extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(sprintf(
                '%s component requires the intl PHP extension',
                __NAMESPACE__
            ));
        }

        // Delaying initialization until we know ext/intl is available
        $this->dateType = IntlDateFormatter::LONG;
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
            "/([ -,.\/]*(?:'[a-zA-Z]+')*[ -,.\/]+)/",
            $pattern,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $result = [];
        foreach ($pregResult as $value) {
            if (stripos($value, "'") === false && stripos($value, 'd') !== false) {
                $result['day'] = $value;
            } elseif (stripos($value, "'") === false && stripos($value, 'm') !== false) {
                $result['month'] = $value;
            } elseif (stripos($value, "'") === false && stripos($value, 'y') !== false) {
                $result['year'] = $value;
            } elseif ($renderDelimiters) {
                $result[] = str_replace("'", '', $value);
            }
        }

        return $result;
    }

    /**
     * Retrieve pattern to use for Date rendering
     */
    public function getPattern(): string
    {
        if (null === $this->pattern) {
            $intl          = new IntlDateFormatter($this->getLocale(), $this->dateType, IntlDateFormatter::NONE);
            $this->pattern = $intl->getPattern();
        }

        return $this->pattern;
    }

    /**
     * Set date formatter
     *
     * @return $this
     */
    public function setDateType(int $dateType)
    {
        // The FULL format uses values that are not used
        if ($dateType === IntlDateFormatter::FULL) {
            $dateType = IntlDateFormatter::LONG;
        }

        $this->dateType = $dateType;

        return $this;
    }

    /**
     * Get date formatter
     */
    public function getDateType(): int
    {
        return $this->dateType;
    }

    /**
     * Set locale
     *
     * @return $this
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get locale
     */
    public function getLocale(): string
    {
        if (null === $this->locale) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Create a key => value options for months
     *
     * @param string $pattern Pattern to use for months
     * @return array
     */
    protected function getMonthsOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            'MM'
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
        for ($month = 1; $month <= 12; $month++) {
            $key          = $keyFormatter->format($date->getTimestamp());
            $value        = $valueFormatter->format($date->getTimestamp());
            $result[$key] = $value;

            $date->modify('+1 month');
        }

        return $result;
    }

    /**
     * Create a key => value options for years
     * NOTE: we don't use a pattern for years, as years written as two digits can lead to hard to
     * read date for users, so we only use four digits years
     *
     * @return array
     */
    protected function getYearsOptions(int $minYear, int $maxYear): array
    {
        $result = [];
        for ($i = $maxYear; $i >= $minYear; --$i) {
            $result[$i] = $i;
        }

        return $result;
    }

    /**
     * Retrieve the FormSelect helper
     */
    protected function getSelectElementHelper(): FormSelect
    {
        if (null !== $this->selectHelper) {
            return $this->selectHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $selectHelper = $this->view->plugin('formselect');
            assert($selectHelper instanceof FormSelect);
            $this->selectHelper = $selectHelper;
        }
        assert(null !== $this->selectHelper);

        return $this->selectHelper;
    }
}
