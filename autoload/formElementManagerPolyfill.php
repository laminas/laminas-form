<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;

call_user_func(function () {
    $target = method_exists(ServiceManager::class, 'configure')
        ? FormElementManager\FormElementManagerV3Polyfill::class
        : FormElementManager\FormElementManagerV2Polyfill::class;

    class_alias($target, FormElementManager::class);
});
