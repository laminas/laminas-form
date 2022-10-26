# Form Element Manager

The form element manager is a *specialized* dependency injection container to obtain objects which implements the `Laminas\Form\ElementInterface` interface.

The manager is the central object of laminas-form to create and retrieve all types of form elements, fieldsets and forms.

It handles all included form elements of laminas-form, user-defined elements and also whole forms.

The manager is based on the [plugin manager of laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/plugin-managers/).

## Benefits

The following benefits are provided by the manager:

- Handles all forms, fieldsets and elements, including all custom ones.
- Create forms, fieldsets and elements with all dependencies like validators and filters.
- Can create forms, fieldsets and elements with name and options.
- Can provide elements, fieldsets and forms without prior registration.
- Fetches hydrators and input filters from the application's service container and add them to a form or a fieldset.
- Allows to override existing elements or to extend them.

## Create a Form Element Manager

To create an instance, the form element manager requires a PSR-11 dependency container. The following examples uses the container implementation of [laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/):

```php
$formElementManager = new Laminas\Form\FormElementManager(
    new Laminas\ServiceManager\ServiceManager()
);
```

## Fetch a Standard Element

To get a supplied form element, use the class name of the element:

```php
$element = $formElementManager->get(Laminas\Form\Element\Select::class);
```

## Fetch and Configure a Standard Element

Internally, the included factory `Laminas\Form\ElementFactory::class` for all types of elements, fieldsets and forms is used to create an element. This factory allows to configure an element, fieldset or form on creation:

```php
$element = $formElementManager->get(
    Laminas\Form\Element\Select::class,
    [
        'name'    => 'rating',
        'options' => [
            'label'         => 'Rating',
            'value_options' => [1, 2, 3, 4, 5],
            ],
        ],
    ]
);
```

The name for the element and the array with options are set as parameter to the constructor of the object on instantiation.

```php
public function __construct($name = null, iterable $options = []) {}
```

```php
$element->getName(); // rating
$element->getLabel(); // Rating
$element->getValueOptions(); // [1, 2, 3, 4, 5]
```

## Fetch a Custom Element without Registration

The form element manager allows to fetch custom elements without prior registration on the manager.

The following example creates a custom element:

```php
final class ExampleElement extends Laminas\Form\Element
{
    // …
}
```

The form element manager can create these custom element by the related class name:

```php
$element = $formElementManager->get(ExampleElement::class);
```

Also here, the included factory `Laminas\Form\ElementFactory::class` is used by the manager to instance the element which allows the pass the name and options like before:

```php
$element = $formElementManager->get(
    ExampleElement::class,
    [
        'name'    => '…',
        'options' => [ 
            // …
        ],
    ]
);
```

## Register and Fetch a Custom Element with a Factory

If a custom element needs some preparation for creation then a factory can be used.

The following example creates a class as factory for the element:

```php
final class ExampleElementFactory
{
    public function __invoke(Psr\Container\ContainerInterface): ExampleElement
    {
        $element = new ExampleElement();
        $element->setOption('example_param', 'value');
        
        return $element;
    }
}
```

Register the custom element on the form element manager by using a factory class:

```php
$formElementManager->setFactory(
    ExampleElement::class,
    ExampleElementFactory::class
);
```

Get the custom element and the option:

```php
$element = $formElementManager->get(ExampleElement::class);

echo $form->getOption('example_param'); // value
```

Now the custom factory will be used to instance the element.

## Fetch a Form without Registration

The form element manager handles also whole forms.

The following example creates a class as form:

```php
final class ExampleForm extends Laminas\Form\Form
{
    public function init(): void
    {
        // …
    }
}
```

The form element manager can create the form by the related class name:

```php
$form = $formElementManager->get(ExampleForm::class);
```

If no separate factory is required, then the form element manager will be instantiating the form class directly, by using the standard factory for elements `Laminas\Form\ElementFactory::class`.

## Register and Fetch a Form With a Factory

If a form needs some preparation for creation then a factory can be used.

The following example creates a class as factory for the form:

```php
final class ExampleFormFactory
{
    public function __invoke(Psr\Container\ContainerInterface): ExampleForm
    {
        $form = new ExampleForm();
        $form->setName('example');
        
        return $form;
    }
}
```

Register the form on the form element manager by using a factory class:

```php
$formElementManager->setFactory(
    ExampleForm::class,
    ExampleFormFactory::class
);
```

Get the form and the name:

```php
$form = $formElementManager->get(ExampleForm::class);

echo $form->getName(); // example
```

Now the custom factory will be used to instance the form.

## Set a Paramater to a Form on Instantiation

The options of a form can be used to set custom parameters to a form.
Create a class for the form and get a custom option:

```php
final class ExampleForm extends Laminas\Form\Form
{
    public function init(): void
    {
        /** @var mixed|null $exampleParam */
        $exampleParam = $this->getOption('example_param');

        // …
    }
}
```

The standard factory `Laminas\Form\ElementFactory::class` for form elements and forms is used which sets the name and/or the options to the object on creation via constructor:

```php
$form = $formElementManager->get(
    ExampleForm::class,
    [
        'options' => [ 
            'example_param' => 'value',
        ],
    ]
);

echo $form->getOption('example_param'); // value
```

## Usage of the Form Element Manager in a Form

If an element is added to a form via the `add` method and the definition of the element is provided via an array then the form factory `Laminas\Form\Factory::class` will be used to create this element.
The form factory uses the form element manager to fetch the element.
The following example uses a custom element in a form:

```php
final class ExampleForm extends Laminas\Form\Form
{
    public function init(): void
    {
        $this->add([
            'type'    => ExampleElement::class,
            'name'    => 'example',
            'options' => [
                // …
            ],
        ]);

        // …
    }
}
```

Fetch the form:

```php
$form = $formElementManager->get(ExampleForm::class);
```

The form element manager will provide the form with the custom element which is created by the form element manager, like before: with or without explicit registration of the element.


## Why Using the Form Element Manager?

- Allows to overwrite or to extend elements without change form specifications.
- Allows decoration of elements, like adding a database adapter without passing the adapter through all layers of a form.
- Handles correct instantiation with configuration and dependencies of elements, fieldsets and forms.
- Allows usage and configuration of custom elements without prior registration of the elements.
- Handles all types of objects of a form; elements, fieldsets and the form itself.

## Learn More

- [The `init` method](advanced.md#initialization)
