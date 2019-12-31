<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage UnitTests
 * @group      Laminas_Form
 */
class FormElementManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormElementManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new FormElementManager();
    }

    public function testInjectToFormFactoryAware()
    {
        $form = $this->manager->get('Form');
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    public function testRegisteringInvalidElementRaisesException()
    {
        $this->setExpectedException('Laminas\Form\Exception\InvalidElementException');
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $this->manager->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Laminas\Form\Exception\InvalidElementException');
        $this->manager->get('test');
    }
}
