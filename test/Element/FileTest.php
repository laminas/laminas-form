<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\File as FileElement;
use Laminas\InputFilter\Factory as InputFilterFactory;
use PHPUnit_Framework_TestCase as TestCase;

class FileTest extends TestCase
{
    public function testProvidesDefaultInputSpecification()
    {
        $element = new FileElement('foo');
        $this->assertEquals('file', $element->getAttribute('type'));

        $inputSpec = $element->getInputSpecification();
        $factory = new InputFilterFactory();
        $input = $factory->createInput($inputSpec);
        $this->assertInstanceOf('Laminas\InputFilter\FileInput', $input);
    }

    public function testWillAddFileEnctypeAttributeToForm()
    {
        $file = new FileElement('foo');
        $formMock = $this->getMock('Laminas\Form\Form');
        $formMock->expects($this->exactly(1))
            ->method('setAttribute')
            ->with($this->stringContains('enctype'),
                   $this->stringContains('multipart/form-data'));
        $file->prepareElement($formMock);
    }
}
