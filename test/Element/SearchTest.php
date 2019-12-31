<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Search;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testType()
    {
        $element = new Search('test');

        $this->assertSame('search', $element->getAttribute('type'));
    }
}
