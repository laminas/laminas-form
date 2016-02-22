<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DateTime;

class CustomCreatedFormFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    private $creationOptions = [];

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $creationString = 'now';
        if (isset($this->creationOptions['created'])) {
            $creationString = $this->creationOptions['created'];
            unset($this->creationOptions['created']);
        }

        $created = new DateTime($creationString);

        $name = null;
        if (isset($this->creationOptions['name'])) {
            $name = $this->creationOptions['name'];
            unset($this->creationOptions['name']);
        }

        $options = $this->creationOptions;

        $form = new CustomCreatedForm($created, $name, $options);
        return $form;
    }

    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
