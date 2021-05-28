<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Search;
use PHPUnit\Framework\TestCase;

final class SearchTest extends TestCase
{
    public function testType(): void
    {
        $element = new Search('test');

        $this->assertSame('search', $element->getAttribute('type'));
    }
}
