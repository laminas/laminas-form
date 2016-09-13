<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\Integration\TestAsset;

use Zend\Form\Element;

class InitializableElement extends Element
{
    /**
     * @var int
     */
    public $dependency = 0;

    /**
     * @var null|int
     */
    public $dependencyAtTimeOfInit;

    /**
     * Initialize element.
     *
     * {@inheritDoc}
     */
    public function init()
    {
        $this->dependencyAtTimeOfInit = $this->dependency;
    }
}
