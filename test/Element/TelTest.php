<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripNewlines;
use Laminas\Form\Element\Tel;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

use function array_diff;
use function array_map;

final class TelTest extends TestCase
{
    public function testType(): void
    {
        $element = new Tel('test');

        self::assertSame('tel', $element->getAttribute('type'));
    }

    public function testInputSpecification(): void
    {
        $name    = 'test';
        $element = new Tel($name);

        $inputSpec = $element->getInputSpecification();

        self::assertSame($name, $inputSpec['name']);
        self::assertTrue($inputSpec['required']);
        $expectedFilters = [StringTrim::class, StripNewlines::class];
        self::assertInputSpecContainsFilters($expectedFilters, $inputSpec);
        self::assertInputSpecContainsRegexValidator($inputSpec);
    }

    /**
     * @param string[] $expectedFilters
     */
    private function assertInputSpecContainsFilters(array $expectedFilters, array $inputSpec): void
    {
        $actualFilters  = array_map(static fn(array $filterSpec): string => $filterSpec['name'], $inputSpec['filters']);
        $missingFilters = array_diff($expectedFilters, $actualFilters);
        self::assertCount(0, $missingFilters);
    }

    private function assertInputSpecContainsRegexValidator(array $inputSpec): void
    {
        $regexValidatorFound = false;
        foreach ($inputSpec['validators'] as $validator) {
            if ($validator instanceof Regex && $validator->getPattern() === "/^[^\r\n]*$/") {
                $regexValidatorFound = true;
            }
        }
        self::assertTrue($regexValidatorFound);
    }
}
