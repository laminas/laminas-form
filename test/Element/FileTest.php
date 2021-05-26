<?php

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\File as FileElement;
use Laminas\Form\Form;
use Laminas\InputFilter\Factory as InputFilterFactory;
use Laminas\InputFilter\FileInput;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testProvidesDefaultInputSpecification(): void
    {
        $element = new FileElement('foo');
        $this->assertEquals('file', $element->getAttribute('type'));

        $inputSpec = $element->getInputSpecification();
        $factory   = new InputFilterFactory();
        $input     = $factory->createInput($inputSpec);
        $this->assertInstanceOf(FileInput::class, $input);
    }

    public function testWillAddFileEnctypeAttributeToForm(): void
    {
        $file     = new FileElement('foo');
        $formMock = $this->createMock(Form::class);
        $formMock->expects($this->once())
            ->method('setAttribute')
            ->with(
                $this->stringContains('enctype'),
                $this->stringContains('multipart/form-data')
            );
        $file->prepareElement($formMock);
    }
}
