# Creation via Factory

You can create the entire form and input filter at once using the `Factory`.
This is particularly nice if you want to store your forms as pure configuration;
you can then pass the configuration to the factory and be done.

```php
use Laminas\Form\Element;
use Laminas\Form\Factory;
use Laminas\Hydrator\ArraySerializable;

$factory = new Factory();
$form    = $factory->createForm([
    'hydrator' => ArraySerializable::class,
    'elements' => [
        [
            'spec' => [
                'name' => 'name',
                'options' => [
                    'label' => 'Your name',
                ],
                'type'  => 'Text',
            ],
        ],
        [
            'spec' => [
                'type' => Element\Email::class,
                'name' => 'email',
                'options' => [
                    'label' => 'Your email address',
                ]
            ],
        ],
        [
            'spec' => [
                'name' => 'subject',
                'options' => [
                    'label' => 'Subject',
                ],
                'type'  => 'Text',
            ],
        ],
        [
            'spec' => [
                'type' => Element\Textarea::class,
                'name' => 'message',
                'options' => [
                    'label' => 'Message',
                ]
            ],
        ],
        [
            'spec' => [
                'type' => Element\Captcha::class,
                'name' => 'captcha',
                'options' => [
                    'label' => 'Please verify you are human.',
                    'captcha' => [
                        'class' => 'Dumb',
                    ],
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Element\Csrf::class,
                'name' => 'security',
            ],
        ],
        [
            'spec' => [
                'name' => 'send',
                'type'  => 'Submit',
                'attributes' => [
                    'value' => 'Submit',
                ],
            ],
        ],
    ],

    /* If we had fieldsets, they'd go here; fieldsets contain
     * "elements" and "fieldsets" keys, and potentially a "type"
     * key indicating the specific FieldsetInterface
     * implementation to use.
    'fieldsets' => [
    ],
     */

    // Configuration to pass on to
    // Laminas\InputFilter\Factory::createInputFilter()
    'input_filter' => [
        /* ... */
    ],
]);
```

If we wanted to use fieldsets, as we demonstrated in the previous example, we
could do the following:

```php
use Laminas\Form\Element;
use Laminas\Form\Factory;
use Laminas\Hydrator\ArraySerializable;

$factory = new Factory();
$form    = $factory->createForm([
    'hydrator'  => ArraySerializable::class,

    // Top-level fieldsets to define:
    'fieldsets' => [
        [
            'spec' => [
                'name' => 'sender',
                'elements' => [
                    [
                        'spec' => [
                            'name' => 'name',
                            'options' => [
                                'label' => 'Your name',
                            ],
                            'type' => 'Text'
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Email::class,
                            'name' => 'email',
                            'options' => [
                                'label' => 'Your email address',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'spec' => [
                'name' => 'details',
                'elements' => [
                    [
                        'spec' => [
                            'name' => 'subject',
                            'options' => [
                                'label' => 'Subject',
                            ],
                            'type' => 'Text',
                        ],
                    ],
                    [
                        'spec' => [
                            'name' => 'message',
                            'type' => Element\Textarea::class,
                            'options' => [
                                'label' => 'Message',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    // You can specify an "elements" key explicitly:
    'elements' => [
        [
            'spec' => [
                'type' => Element\Captcha::class,
                'name' => 'captcha',
                'options' => [
                    'label' => 'Please verify you are human.',
                    'captcha' => [
                        'class' => 'Dumb',
                    ],
                ],
            ],
        ],
        [
            'spec' => [
            'type' => Element\Csrf::class,
            'name' => 'security',
        ],
    ],

    // But entries without string keys are also considered elements:
    [
        'spec' => [
            'name' => 'send',
            'type'  => 'Submit',
            'attributes' => [
                'value' => 'Submit',
            ],
        ],
    ],

    // Configuration to pass on to
    // Laminas\InputFilter\Factory::createInputFilter()
    'input_filter' => [
        /* ... */
    ],
]);
```

Note that the chief difference is nesting; otherwise, the information is
basically the same.

The chief benefits to using the `Factory` are allowing you to store definitions
in configuration, and usage of significant whitespace.
