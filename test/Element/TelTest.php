<?php

namespace LaminasTest\Form\Element;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripNewlines;
use Laminas\Form\Element\Tel;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

use function array_diff;
use function array_map;

class TelTest extends TestCase
{
    public function testType(): void
    {
        $element = new Tel('test');

        $this->assertSame('tel', $element->getAttribute('type'));
    }

    public function testInputSpecification(): void
    {
        $name    = 'test';
        $element = new Tel($name);

        $inputSpec = $element->getInputSpecification();

        $this->assertSame($name, $inputSpec['name']);
        $this->assertTrue($inputSpec['required']);
        $expectedFilters = [StringTrim::class, StripNewlines::class];
        $this->assertInputSpecContainsFilters($expectedFilters, $inputSpec);
        $this->assertInputSpecContainsRegexValidator($inputSpec);
    }

    /**
     * @param string[] $expectedFilters
     * @param array $inputSpec
     */
    private function assertInputSpecContainsFilters(array $expectedFilters, array $inputSpec): void
    {
        $actualFilters  = array_map(static function (array $filterSpec): string {
            return $filterSpec['name'];
        }, $inputSpec['filters']);
        $missingFilters = array_diff($expectedFilters, $actualFilters);
        $this->assertCount(0, $missingFilters);
    }

    /**
     * @param array $inputSpec
     */
    private function assertInputSpecContainsRegexValidator(array $inputSpec): void
    {
        $regexValidatorFound = false;
        foreach ($inputSpec['validators'] as $validator) {
            if ($validator instanceof Regex && $validator->getPattern() === "/^[^\r\n]*$/") {
                $regexValidatorFound = true;
            }
        }
        $this->assertTrue($regexValidatorFound);
    }
}
