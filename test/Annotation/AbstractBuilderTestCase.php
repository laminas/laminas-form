<?php

namespace LaminasTest\Form\Annotation;

use Generator;
use Laminas\Form\Annotation;
use Laminas\Form\Element;
use Laminas\Form\Element\Collection;
use Laminas\Form\Fieldset;
use Laminas\Form\FieldsetInterface;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\ObjectProperty;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputInterface;
use LaminasTest\Form\TestAsset;
use LaminasTest\Form\TestAsset\Annotation\Entity;
use LaminasTest\Form\TestAsset\Annotation\Form;
use LaminasTest\Form\TestAsset\Annotation\InputFilter;
use LaminasTest\Form\TestAsset\Annotation\InputFilterInput;
use PHPUnit\Framework\TestCase;

use function class_exists;
use function getenv;

abstract class AbstractBuilderTestCase extends TestCase
{
    /** @var string */
    private $classMethodsHydratorClass;

    /** @var string */
    private $objectPropertyHydratorClass;

    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_FORM_ANNOTATION_SUPPORT')) {
            $this->markTestSkipped('Enable TESTS_LAMINAS_FORM_ANNOTATION_SUPPORT to test annotation parsing');
        }

        $this->classMethodsHydratorClass = class_exists(ClassMethodsHydrator::class)
            ? ClassMethodsHydrator::class
            : ClassMethods::class;

        $this->objectPropertyHydratorClass = class_exists(ObjectPropertyHydrator::class)
            ? ObjectPropertyHydrator::class
            : ObjectProperty::class;
    }

    abstract protected function createBuilder(): Annotation\AbstractBuilder;

    public function testCanCreateFormFromStandardEntity()
    {
        $entity  = new TestAsset\Annotation\Entity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));

        $username = $form->get('username');
        $this->assertInstanceOf(Element::class, $username);
        $this->assertEquals('required', $username->getAttribute('required'));

        $password = $form->get('password');
        $this->assertInstanceOf(Element::class, $password);
        $attributes = $password->getAttributes();
        $this->assertEquals(
            ['type' => 'password', 'label' => 'Enter your password', 'name' => 'password'],
            $attributes
        );
        $this->assertNull($password->getAttribute('required'));

        $filter = $form->getInputFilter();
        $this->assertTrue($filter->has('username'));
        $this->assertTrue($filter->has('password'));

        $username = $filter->get('username');
        $this->assertTrue($username->isRequired());
        $this->assertCount(1, $username->getFilterChain());
        $this->assertCount(2, $username->getValidatorChain());

        $password = $filter->get('password');
        $this->assertTrue($password->isRequired());
        $this->assertCount(1, $password->getFilterChain());
        $this->assertCount(1, $password->getValidatorChain());
    }

    public function testCanCreateFormWithClassAnnotations()
    {
        $entity  = new TestAsset\Annotation\ClassEntity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('keeper'));
        $this->assertFalse($form->has('keep'));
        $this->assertFalse($form->has('omit'));
        $this->assertEquals('some_name', $form->getName());

        $attributes = $form->getAttributes();
        $this->assertArrayHasKey('legend', $attributes);
        $this->assertEquals('Some Fieldset', $attributes['legend']);

        $filter = $form->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $filter);

        $keeper     = $form->get('keeper');
        $attributes = $keeper->getAttributes();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('text', $attributes['type']);

        $this->assertSame(['omit', 'keep'], $form->getValidationGroup());
    }

    public function testComplexEntityCreationWithPriorities()
    {
        $entity  = new TestAsset\Annotation\ComplexEntity();
        $builder = $this->createBuilder();
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

        $test = $form->getIterator()->current();
        $this->assertSame($email, $test, 'Test is element ' . $test->getName());

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf($this->objectPropertyHydratorClass, $hydrator);
    }

    public function testFieldsetOrder()
    {
        $entity  = new TestAsset\Annotation\FieldsetOrderEntity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $element = $form->get('element');
        $first   = $form->getIterator()->getIterator()->current();
        $this->assertSame($element, $first, 'Test is element ' . $first->getName());
    }

    public function testFieldsetOrderWithPreserve()
    {
        $entity  = new TestAsset\Annotation\FieldsetOrderEntity();
        $builder = $this->createBuilder();
        $builder->setPreserveDefinedOrder(true);
        $form = $builder->createForm($entity);

        $fieldset = $form->get('fieldset');
        $first    = $form->getIterator()->getIterator()->current();
        $this->assertSame($fieldset, $first, 'Test is element ' . $first->getName());
    }

    public function testCanRetrieveOnlyFormSpecification()
    {
        $entity  = new TestAsset\Annotation\ComplexEntity();
        $builder = $this->createBuilder();
        $spec    = $builder->getFormSpecification($entity);
        $this->assertInstanceOf('ArrayObject', $spec);
    }

    public function testAllowsExtensionOfEntities()
    {
        $entity  = new TestAsset\Annotation\ExtendedEntity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));
        $this->assertTrue($form->has('email'));

        $this->assertEquals('extended', $form->getName());
        $expected = ['username', 'password', 'email'];
        $test     = [];
        foreach ($form as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testAllowsSpecifyingFormAndElementTypes()
    {
        $entity  = new TestAsset\Annotation\TypedEntity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertInstanceOf(Form::class, $form);
        $element = $form->get('typed_element');
        $this->assertInstanceOf(\LaminasTest\Form\TestAsset\Annotation\Element::class, $element);
    }

    public function testAllowsComposingChildEntities()
    {
        $entity  = new TestAsset\Annotation\EntityComposingAnEntity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('composed'));
        $composed = $form->get('composed');
        $this->assertInstanceOf(FieldsetInterface::class, $composed);
        $this->assertTrue($composed->has('username'));
        $this->assertTrue($composed->has('password'));

        $filter = $form->getInputFilter();
        $this->assertTrue($filter->has('composed'));
        $composed = $filter->get('composed');
        $this->assertInstanceOf(InputFilterInterface::class, $composed);
        $this->assertTrue($composed->has('username'));
        $this->assertTrue($composed->has('password'));
    }

    public function testAllowsComposingMultipleChildEntities()
    {
        $entity  = new TestAsset\Annotation\EntityComposingMultipleEntities();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('composed'));
        $composed = $form->get('composed');

        $this->assertInstanceOf(Collection::class, $composed);
        $target = $composed->getTargetElement();
        $this->assertInstanceOf(FieldsetInterface::class, $target);
        $this->assertTrue($target->has('username'));
        $this->assertTrue($target->has('password'));
    }

    /**
     * @dataProvider provideOptionsAnnotationAndComposedObjectAnnotation
     * @param string $childName
     * @group issue-7108
     */
    public function testOptionsAnnotationAndComposedObjectAnnotation($childName)
    {
        $entity  = new TestAsset\Annotation\EntityUsingComposedObjectAndOptions();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $child = $form->get($childName);

        $target = $child->getTargetElement();
        $this->assertInstanceOf(FieldsetInterface::class, $target);
        $this->assertEquals('My label', $child->getLabel());
    }

    /**
     * Data provider
     *
     * @return Generator
     */
    public function provideOptionsAnnotationAndComposedObjectAnnotation()
    {
        yield ['child'];
        yield ['childTheSecond'];
    }

    /**
     * @dataProvider provideOptionsAnnotationAndComposedObjectAnnotationNoneCollection
     * @param string $childName
     * @group issue-7108
     */
    public function testOptionsAnnotationAndComposedObjectAnnotationNoneCollection($childName)
    {
        $entity  = new TestAsset\Annotation\EntityUsingComposedObjectAndOptions();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $child = $form->get($childName);

        $this->assertInstanceOf(FieldsetInterface::class, $child);
        $this->assertEquals('My label', $child->getLabel());
    }

    /**
     * Data provider
     *
     * @return Generator
     */
    public function provideOptionsAnnotationAndComposedObjectAnnotationNoneCollection()
    {
        yield ['childTheThird'];
        yield ['childTheFourth'];
    }

    public function testCanHandleOptionsAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityUsingOptions();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->useAsBaseFieldset());

        $this->assertTrue($form->has('username'));

        $username = $form->get('username');
        $this->assertInstanceOf(Element::class, $username);

        $this->assertEquals('Username:', $username->getLabel());
        $this->assertEquals(['class' => 'label'], $username->getLabelAttributes());
    }

    public function testCanHandleHydratorArrayAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityWithHydratorArray();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf($this->classMethodsHydratorClass, $hydrator);
        $this->assertFalse($hydrator->getUnderscoreSeparatedKeys());
    }

    public function testAllowTypeAsElementNameInInputFilter()
    {
        $entity  = new TestAsset\Annotation\EntityWithTypeAsElementName();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $this->assertInstanceOf(\Laminas\Form\Form::class, $form);
        $element = $form->get('type');
        $this->assertInstanceOf(Element::class, $element);
    }

    public function testAllowEmptyInput()
    {
        $entity  = new TestAsset\Annotation\SampleEntity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $inputFilter = $form->getInputFilter();
        $sampleinput = $inputFilter->get('sampleinput');
        $this->assertTrue($sampleinput->allowEmpty());
    }

    public function testContinueIfEmptyInput()
    {
        $entity  = new TestAsset\Annotation\SampleEntity();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $inputFilter = $form->getInputFilter();
        $sampleinput = $inputFilter->get('sampleinput');
        $this->assertTrue($sampleinput->continueIfEmpty());
    }

    public function testInputNotRequiredByDefault()
    {
        $entity      = new TestAsset\Annotation\SampleEntity();
        $builder     = $this->createBuilder();
        $form        = $builder->createForm($entity);
        $inputFilter = $form->getInputFilter();
        $sampleinput = $inputFilter->get('anotherSampleInput');
        $this->assertFalse($sampleinput->isRequired());
    }

    public function testInstanceElementAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityUsingInstanceProperty();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);

        $fieldset = $form->get('object');
        /** @var Laminas\Form\Fieldset $fieldset */

        $this->assertInstanceOf(Fieldset::class, $fieldset);
        $this->assertInstanceOf(Entity::class, $fieldset->getObject());
        $this->assertInstanceOf($this->classMethodsHydratorClass, $fieldset->getHydrator());
        $this->assertFalse($fieldset->getHydrator()->getUnderscoreSeparatedKeys());
    }

    public function testInputFilterInputAnnotation()
    {
        $entity      = new TestAsset\Annotation\EntityWithInputFilterInput();
        $builder     = $this->createBuilder();
        $form        = $builder->createForm($entity);
        $inputFilter = $form->getInputFilter();

        $this->assertTrue($inputFilter->has('input'));
        $expected = [
            InputInterface::class,
            InputFilterInput::class,
        ];
        foreach ($expected as $expectedInstance) {
            $this->assertInstanceOf($expectedInstance, $inputFilter->get('input'));
        }
    }

    /**
     * @group issue-6753
     */
    public function testInputFilterAnnotationAllowsComposition()
    {
        $entity      = new TestAsset\Annotation\EntityWithInputFilterAnnotation();
        $builder     = $this->createBuilder();
        $form        = $builder->createForm($entity);
        $inputFilter = $form->getInputFilter();
        $this->assertCount(2, $inputFilter->get('username')->getValidatorChain());
    }

    public function testLegacyComposedObjectAnnotation()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessageMatches('/Passing a single array .* is deprecated/');
        $entity  = new TestAsset\Annotation\LegacyComposedObjectAnnotation();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);
    }

    public function testLegacyStyleFilterAnnotations()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessageMatches('/Passing a single array .* is deprecated/');
        $entity  = new TestAsset\Annotation\LegacyFilterAnnotation();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);
    }

    public function testLegacyStyleHydratorAnnotations()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessageMatches('/Passing a single array .* is deprecated/');
        $entity  = new TestAsset\Annotation\LegacyHydratorAnnotation();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);
    }

    public function testLegacyStyleValidatorAnnotations()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessageMatches('/Passing a single array .* is deprecated/');
        $entity  = new TestAsset\Annotation\LegacyValidatorAnnotation();
        $builder = $this->createBuilder();
        $form    = $builder->createForm($entity);
    }
}
