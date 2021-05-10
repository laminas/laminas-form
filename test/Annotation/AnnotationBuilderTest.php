<?php

namespace LaminasTest\Form\Annotation;

use Laminas\Form\Annotation;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\ObjectProperty;
use Laminas\Hydrator\ObjectPropertyHydrator;
use LaminasTest\Form\TestAsset;
use PHPUnit\Framework\TestCase;

use function class_exists;
use function count;
use function getenv;
use function restore_error_handler;
use function set_error_handler;
use function version_compare;

use const E_USER_DEPRECATED;
use const PHP_VERSION;

class AnnotationBuilderTest extends TestCase
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

        $this->assertSame(['omit', 'keep'], $form->getValidationGroup());
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

        $test  = $form->getIterator()->current();
        $this->assertSame($email, $test, 'Test is element ' . $test->getName());

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf($this->objectPropertyHydratorClass, $hydrator);
    }

    public function testFieldsetOrder()
    {
        $entity  = new TestAsset\Annotation\FieldsetOrderEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $element = $form->get('element');
        $first  = $form->getIterator()->getIterator()->current();
        $this->assertSame($element, $first, 'Test is element ' . $first->getName());
    }

    public function testFieldsetOrderWithPreserve()
    {
        $entity  = new TestAsset\Annotation\FieldsetOrderEntity();
        $builder = new Annotation\AnnotationBuilder();
        $builder->setPreserveDefinedOrder(true);
        $form    = $builder->createForm($entity);

        $fieldset = $form->get('fieldset');
        $first  = $form->getIterator()->getIterator()->current();
        $this->assertSame($fieldset, $first, 'Test is element ' . $first->getName());
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

    /**
     * @dataProvider provideOptionsAnnotationAndComposedObjectAnnotation
     * @param string $childName
     *
     * @group issue-7108
     */
    public function testOptionsAnnotationAndComposedObjectAnnotation($childName)
    {
        $entity  = new TestAsset\Annotation\EntityUsingComposedObjectAndOptions();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $child = $form->get($childName);

        $target = $child->getTargetElement();
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $target);
        $this->assertEquals('My label', $child->getLabel());
    }

    /**
     * Data provider
     *
     * @return string[][]
     */
    public function provideOptionsAnnotationAndComposedObjectAnnotation()
    {
        return [['child'], ['childTheSecond']];
    }

    /**
     * @dataProvider provideOptionsAnnotationAndComposedObjectAnnotationNoneCollection
     * @param string $childName
     *
     * @group issue-7108
     */
    public function testOptionsAnnotationAndComposedObjectAnnotationNoneCollection($childName)
    {
        $entity  = new TestAsset\Annotation\EntityUsingComposedObjectAndOptions();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $child = $form->get($childName);

        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $child);
        $this->assertEquals('My label', $child->getLabel());
    }

    /**
     * Data provider
     *
     * @return string[][]
     */
    public function provideOptionsAnnotationAndComposedObjectAnnotationNoneCollection()
    {
        return [['childTheThird'], ['childTheFourth']];
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
        $this->assertEquals(['class' => 'label'], $username->getLabelAttributes());
    }

    public function testCanHandleHydratorArrayAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityWithHydratorArray();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf($this->classMethodsHydratorClass, $hydrator);
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

    public function testContinueIfEmptyInput()
    {
        $entity  = new TestAsset\Annotation\SampleEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $inputFilter = $form->getInputFilter();
        $sampleinput = $inputFilter->get('sampleinput');
        $this->assertTrue($sampleinput->continueIfEmpty());
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
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $this->markTestSkipped('Cannot test Object annotation in PHP 7; object is a reserved keyword');
        }

        $entity = new TestAsset\Annotation\EntityUsingObjectProperty();
        $builder = new Annotation\AnnotationBuilder();

        $phpunit = $this;
        set_error_handler(function ($code, $message) use ($phpunit) {
            $phpunit->assertEquals(E_USER_DEPRECATED, $code);
        }, E_USER_DEPRECATED);
        $form = $builder->createForm($entity);
        restore_error_handler();

        $fieldset = $form->get('object');
        /* @var $fieldset Laminas\Form\Fieldset */

        $this->assertInstanceOf('Laminas\Form\Fieldset', $fieldset);
        $this->assertInstanceOf('LaminasTest\Form\TestAsset\Annotation\Entity', $fieldset->getObject());
        $this->assertInstanceOf($this->classMethodsHydratorClass, $fieldset->getHydrator());
        $this->assertFalse($fieldset->getHydrator()->getUnderscoreSeparatedKeys());
    }

    public function testInstanceElementAnnotation()
    {
        $entity = new TestAsset\Annotation\EntityUsingInstanceProperty();
        $builder = new Annotation\AnnotationBuilder();
        $form = $builder->createForm($entity);

        $fieldset = $form->get('object');
        /* @var $fieldset Laminas\Form\Fieldset */

        $this->assertInstanceOf('Laminas\Form\Fieldset', $fieldset);
        $this->assertInstanceOf('LaminasTest\Form\TestAsset\Annotation\Entity', $fieldset->getObject());
        $this->assertInstanceOf($this->classMethodsHydratorClass, $fieldset->getHydrator());
        $this->assertFalse($fieldset->getHydrator()->getUnderscoreSeparatedKeys());
    }

    public function testInputFilterInputAnnotation()
    {
        $entity = new TestAsset\Annotation\EntityWithInputFilterInput();
        $builder = new Annotation\AnnotationBuilder();
        $form = $builder->createForm($entity);
        $inputFilter = $form->getInputFilter();

        $this->assertTrue($inputFilter->has('input'));
        $expected = [
            'Laminas\InputFilter\InputInterface',
            'LaminasTest\Form\TestAsset\Annotation\InputFilterInput',
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
        $entity = new TestAsset\Annotation\EntityWithInputFilterAnnotation();
        $builder = new Annotation\AnnotationBuilder();
        $form = $builder->createForm($entity);
        $inputFilter = $form->getInputFilter();
        $this->assertCount(2, $inputFilter->get('username')->getValidatorChain());
    }
}
