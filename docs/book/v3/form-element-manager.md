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

> INFO: **Stand-Alone Usage**
> The following examples show the basics of the form element manager using stand-alone usage.
>
> The configuration and usage in **laminas-mvc** or **Mezzio** based application [can be found in separate sections](#learn-more).

## Create a Form Element Manager

To create an instance, the form element manager requires a PSR-11 dependency container.
The following examples uses the container implementation of [laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/):

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

The form element manager uses the factory `Laminas\Form\ElementFactory` to create all elements, fieldsets, and forms.
This factory allows the configuration of an element during fetching:

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

The name for the element and the options array are provided as parameters to the associated class constructor on instantiation:

```php
public function __construct($name = null, iterable $options = []) {}
```

Retrieving the name and the set options:

```php
echo $element->getName(); // rating
echo $element->getLabel(); // Rating

$valueOptions = $element->getValueOptions(); // [1, 2, 3, 4, 5]
```

## Fetch a Custom Element without Registration

The form element manager allows fetching custom elements without prior registration with the manager.

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

The manager uses the factory `Laminas\Form\ElementFactory` to instantiate the element, and will pass the name and options array just like in the prior example:

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

If no separate factory is required, then the form element manager will be instantiating the form class directly, by using the standard factory for elements (`Laminas\Form\ElementFactory`).

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

* It can be hooked into the initialization via the `init()` method.
* The form element factory calls the `init()` method after creating an instance of the class.
* Since the name and options are processed in the constructor, it can be accessed them via `init()` to perform further customizations for the instance.

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

The standard factory `Laminas\Form\ElementFactory` for form elements and forms is used which sets the name and/or the options to the object on creation via constructor:

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

If an element is added to a form via the `add` method and the definition of the element is provided via an array then the form factory `Laminas\Form\Factory` will be used to create this element.
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
                'label' => 'Example element'
            ],
        ]);

        // …
    }
}
```

Fetch the form and the element:

```php
$form = $formElementManager->get(ExampleForm::class);

echo $form->get('example')->getLabel(); // Example element
```

The form element manager will provide the form with the custom element which is created by the form element manager, like before: with or without explicit registration of the element.

## Configuring the Form Element Manager

The manager is based on the [plugin manager of laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/plugin-managers/) and the [configuration follows the exact same pattern](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/) as for a normal service manager of laminas-servicemanager:

```php
$formElementManager = new Laminas\Form\FormElementManager(
    new Laminas\ServiceManager\ServiceManager(),
    [
        'factories'          => [
            Album\Form\ExampleElement::class => Album\Form\ExampleElementFactory::class,
        ],
        'aliases'            => [
            'example' => Album\Form\ExampleElement::class,
        ],
        'abstract_factories' => [],
        'delegators'         => [],
        // …
    ]
);
```

## Why Use the Form Element Manager?

- It allows overwriting or extending elements without changing form specifications.
- It allows decoration of elements, like adding a database adapter without passing the adapter through all layers of a form.
- It manages instantiation and initialization of elements, fieldsets and forms, including all dependencies and configuration options.
- It allows usage and configuration of custom elements without prior registration of the elements.
- It handles all form object types: elements, fieldsets and the form itself.

## Learn More

- [Usage of Form Element Manager in a laminas-mvc Application](application-integration/form-element-manager-mvc.md)
- [The `init` method](advanced.md#initialization)
- [Configuring the service manager](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/)
