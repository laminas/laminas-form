<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset;
use PHPUnit_Framework_TestCase as TestCase;

class AnnotationBuilderTest extends TestCase
{
    public function setUp()
    {
        if (!defined('TESTS_LAMINAS_FORM_ANNOTATION_SUPPORT')
            || !constant('TESTS_LAMINAS_FORM_ANNOTATION_SUPPORT')
        ) {
            $this->markTestSkipped('Enable TESTS_LAMINAS_FORM_ANNOTATION_SUPPORT to test annotation parsing');
        }
    }

    public function testCanCreateFormFromStandardEntity()
    {
        $entity  = new TestAsset\Annotation\Entity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));

        $username = $form->get('username');
        $this->assertInstanceOf('Laminas\Form\Element', $username);
        $this->assertEquals('required', $username->getAttribute('required'));

        $password = $form->get('password');
        $this->assertInstanceOf('Laminas\Form\Element', $password);
        $attributes = $password->getAttributes();
        $this->assertEquals(array('type' => 'password', 'label' => 'Enter your password', 'name' => 'password'), $attributes);
        $this->assertNull($password->getAttribute('required'));

        $filter = $form->getInputFilter();
        $this->assertTrue($filter->has('username'));
        $this->assertTrue($filter->has('password'));

        $username = $filter->get('username');
        $this->assertTrue($username->isRequired());
        $this->assertEquals(1, count($username->getFilterChain()));
        $this->assertEquals(2, count($username->getValidatorChain()));

        $password = $filter->get('password');
        $this->assertTrue($password->isRequired());
        $this->assertEquals(1, count($password->getFilterChain()));
        $this->assertEquals(1, count($password->getValidatorChain()));
    }

    public function testCanCreateFormWithClassAnnotations()
    {
        $entity  = new TestAsset\Annotation\ClassEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('keeper'));
        $this->assertFalse($form->has('keep'));
        $this->assertFalse($form->has('omit'));
        $this->assertEquals('some_name', $form->getName());

        $attributes = $form->getAttributes();
        $this->assertArrayHasKey('legend', $attributes);
        $this->assertEquals('Some Fieldset', $attributes['legend']);

        $filter = $form->getInputFilter();
        $this->assertInstanceOf('LaminasTest\Form\TestAsset\Annotation\InputFilter', $filter);

        $keeper     = $form->get('keeper');
        $attributes = $keeper->getAttributes();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('text', $attributes['type']);

        $this->assertObjectHasAttribute('validationGroup', $form);
        $this->assertAttributeEquals(array('omit', 'keep'), 'validationGroup', $form);
    }

    public function testComplexEntityCreationWithPriorities()
    {
        $entity  = new TestAsset\Annotation\ComplexEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertEquals('user', $form->getName());
        $attributes = $form->getAttributes();
        $this->assertArrayHasKey('legend', $attributes);
        $this->assertEquals('Register', $attributes['legend']);

        $this->assertFalse($form->has('someComposedObject'));
        $this->assertTrue($form->has('user_image'));
        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('password'));
        $this->assertTrue($form->has('username'));

        $email = $form->get('email');
        $test  = $form->getIterator()->getIterator()->current();
        $this->assertSame($email, $test, 'Test is element ' . $test->getName());

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $hydrator);
    }

    public function testCanRetrieveOnlyFormSpecification()
    {
        $entity  = new TestAsset\Annotation\ComplexEntity();
        $builder = new Annotation\AnnotationBuilder();
        $spec    = $builder->getFormSpecification($entity);
        $this->assertInstanceOf('ArrayObject', $spec);
    }

    public function testAllowsExtensionOfEntities()
    {
        $entity  = new TestAsset\Annotation\ExtendedEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));
        $this->assertTrue($form->has('email'));

        $this->assertEquals('extended', $form->getName());
        $expected = array('username', 'password', 'email');
        $test     = array();
        foreach ($form as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testAllowsSpecifyingFormAndElementTypes()
    {
        $entity  = new TestAsset\Annotation\TypedEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertInstanceOf('LaminasTest\Form\TestAsset\Annotation\Form', $form);
        $element = $form->get('typed_element');
        $this->assertInstanceOf('LaminasTest\Form\TestAsset\Annotation\Element', $element);
    }

    public function testAllowsComposingChildEntities()
    {
        $entity  = new TestAsset\Annotation\EntityComposingAnEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('composed'));
        $composed = $form->get('composed');
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $composed);
        $this->assertTrue($composed->has('username'));
        $this->assertTrue($composed->has('password'));

        $filter = $form->getInputFilter();
        $this->assertTrue($filter->has('composed'));
        $composed = $filter->get('composed');
        $this->assertInstanceOf('Laminas\InputFilter\InputFilterInterface', $composed);
        $this->assertTrue($composed->has('username'));
        $this->assertTrue($composed->has('password'));
    }

    public function testAllowsComposingMultipleChildEntities()
    {
        $entity  = new TestAsset\Annotation\EntityComposingMultipleEntities();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('composed'));
        $composed = $form->get('composed');

        $this->assertInstanceOf('Laminas\Form\Element\Collection', $composed);
        $target = $composed->getTargetElement();
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $target);
        $this->assertTrue($target->has('username'));
        $this->assertTrue($target->has('password'));
    }

    public function testCanHandleOptionsAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityUsingOptions();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->useAsBaseFieldset());

        $this->assertTrue($form->has('username'));

        $username = $form->get('username');
        $this->assertInstanceOf('Laminas\Form\Element', $username);

        $this->assertEquals('Username:', $username->getLabel());
        $this->assertEquals(array('class' => 'label'), $username->getLabelAttributes());
    }

    public function testCanHandleHydratorArrayAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityWithHydratorArray();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ClassMethods', $hydrator);
        $this->assertFalse($hydrator->getUnderscoreSeparatedKeys());
    }

    public function testAllowTypeAsElementNameInInputFilter()
    {
        $entity  = new TestAsset\Annotation\EntityWithTypeAsElementName();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertInstanceOf('Laminas\Form\Form', $form);
        $element = $form->get('type');
        $this->assertInstanceOf('Laminas\Form\Element', $element);
    }

    public function testAllowEmptyInput()
    {
        $entity  = new TestAsset\Annotation\SampleEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $inputFilter = $form->getInputFilter();
        $sampleinput = $inputFilter->get('sampleinput');
        $this->assertTrue($sampleinput->allowEmpty());
    }

    public function testInputNotRequiredByDefault()
    {
        $entity = new TestAsset\Annotation\SampleEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form = $builder->createForm($entity);
        $inputFilter = $form->getInputFilter();
        $sampleinput = $inputFilter->get('anotherSampleInput');
        $this->assertFalse($sampleinput->isRequired());
    }

    public function testObjectElementAnnotation()
    {
        $entity = new TestAsset\Annotation\EntityUsingObjectProperty();
        $builder = new Annotation\AnnotationBuilder();
        $form = $builder->createForm($entity);

        $fieldset = $form->get('object');
        /* @var $fieldset Laminas\Form\Fieldset */

        $this->assertInstanceOf('Laminas\Form\Fieldset',$fieldset);
        $this->assertInstanceOf('LaminasTest\Form\TestAsset\Annotation\Entity',$fieldset->getObject());
        $this->assertInstanceOf("Laminas\Stdlib\Hydrator\ClassMethods",$fieldset->getHydrator());
        $this->assertFalse($fieldset->getHydrator()->getUnderscoreSeparatedKeys());
    }

    public function testInputFilterInputAnnotation()
    {
        $entity = new TestAsset\Annotation\EntityWithInputFilterInput();
        $builder = new Annotation\AnnotationBuilder();
        $form = $builder->createForm($entity);
        $inputFilter = $form->getInputFilter();

        $this->assertTrue($inputFilter->has('input'));
        foreach (
            array('Laminas\InputFilter\InputInterface', 'LaminasTest\Form\TestAsset\Annotation\InputFilterInput') as
            $expectedInstance
        ) {
            $this->assertInstanceOf($expectedInstance, $inputFilter->get('input'));
        }

    }
}
