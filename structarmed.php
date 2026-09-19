<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PER(), Preset::CODEQUALITY())
    ->layer('Exception', 'src/Exception')
    ->layer('Element', [
        'src/Element.php',
        'src/ElementAttributeRemovalInterface.php',
        'src/ElementInterface.php',
        'src/LabelAwareInterface.php',
        'src/LabelAwareTrait.php',
    ])
    ->layer('Form', [
        'src/Element/CollectionInterface.php',
        'src/ElementPrepareAwareInterface.php',
        'src/Factory.php',
        'src/Fieldset.php',
        'src/FieldsetInterface.php',
        'src/Form.php',
        'src/FormFactoryAwareInterface.php',
        'src/FormFactoryAwareTrait.php',
        'src/FormInterface.php',
        'src/InputFilterProviderFieldset.php',
    ])
    ->layerPattern(
        'Elements',
        '/^Laminas\\\\Form\\\\Element\\\\.*$/',
        '/^Laminas\\\\Form\\\\Element\\\\CollectionInterface$/'
    )
    ->layer('ElementManager', [
        'src/ElementFactory.php',
        'src/FormElementManager.php',
    ])
    ->layer('ServiceFactory', [
        'src/FormAbstractServiceFactory.php',
        'src/FormElementManagerFactory.php',
    ])
    ->layer('Annotation', 'src/Annotation')
    ->layerPattern(
        'ViewHelper',
        '/^Laminas\\\\Form\\\\View\\\\Helper\\\\.*$/',
        [
            '/^Laminas\\\\Form\\\\View\\\\Helper\\\\Captcha\\\\.*$/',
            '/^Laminas\\\\Form\\\\View\\\\Helper\\\\Factory\\\\.*$/',
            '/^Laminas\\\\Form\\\\View\\\\Helper\\\\File\\\\.*$/',
        ]
    )
    ->layer('ViewHelperCaptcha', 'src/View/Helper/Captcha')
    ->layer('ViewHelperFile', 'src/View/Helper/File')
    ->layer('ViewHelperFactory', 'src/View/Helper/Factory')
    ->layer('ViewHelperTrait', 'src/View/HelperTrait.php')
    ->layer('Config', [
        'src/ConfigProvider.php',
        'src/Module.php',
    ])
    ->ruleset([
        'Exception'         => [],
        'Element'           => ['Exception'],
        'Form'              => ['+Element', 'ElementManager'],
        'Elements'          => ['+Form'],
        'ElementManager'    => ['+Elements'],
        'ServiceFactory'    => ['ElementManager', 'Form'],
        'Annotation'        => ['+ElementManager'],
        'ViewHelper'        => ['+Elements'],
        'ViewHelperCaptcha' => ['+ViewHelper'],
        'ViewHelperFile'    => ['+ViewHelper'],
        'ViewHelperFactory' => ['ViewHelper'],
        'ViewHelperTrait'   => ['+ViewHelperCaptcha'],
        'Config'            => [
            '+Annotation',
            '+ViewHelperFactory',
            'ServiceFactory',
            'ViewHelperCaptcha',
            'ViewHelperFile',
        ],
    ]);
