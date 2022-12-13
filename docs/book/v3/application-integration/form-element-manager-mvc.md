# Usage of Form Element Manager in a laminas-mvc Application

INFO:
The following examples show the usage of the form element manager in a laminas-mvc based application.
All **the basics of the form element manager** [can be found in a separate section](../form-element-manager.md).

## Using the Form Element Manager in a Controller

### Create Controller

[Create a controller class](https://docs.laminas.dev/laminas-mvc/quick-start/#create-a-controller) and inject the form element manager via the constructor, e.g. `module/Album/Controller/AlbumController.php`:

```php
namespace Album\Controller;

use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;

final class AlbumController extends AbstractActionController
{
    public function __construct(
        public readonly FormElementManager $formElementManager
    ) {}
}
```

### Register Controller

In a laminas-mvc based application, the form element manager is registered in the application during the [installation of laminas-form](../installation.md#installation-for-mezzio-and-laminas-mvc-application).
This allows to fetch the form element manager from the application service container.
With the [reflection factory of laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/reflection-abstract-factory/), the form element manager can be automaticly injected into the controller.

To [register the controller](https://docs.laminas.dev/laminas-mvc/quick-start/#create-a-route) for the application, extend the configuration of the module.
Add the following lines to the module configuration file, e.g. `module/Album/config/module.config.php`:

<pre class="language-php" data-line="8-9"><code>
namespace Album;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

return [
    'controllers' => [
        'factories' => [
            // Add this line
            Controller\AlbumController::class => ReflectionBasedAbstractFactory::class,
        ],
    ],
    // …
];
</code></pre>

## Fetch a Form without Registration

The following example creates a class as form:

```php
final class AlbumForm extends Laminas\Form\Form
{
    public function init(): void
    {
        // …
    }
}
```

Extend the controller and fetch the form:

<pre class="language-php" data-line="3,7,17-18"><code>
namespace Album\Controller;

use Album\Form\AlbumForm;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;

use function assert;

final class AlbumController extends AbstractActionController
{
    public function __construct(
        public readonly FormElementManager $formElementManager
    ) {}
    
    public function addAction()
    {
        $form = $this->formElementManager->get(AlbumForm::class);
        assert($form instanceof AlbumForm);
        
        // …
    }
}
</code></pre>

## Register and Fetch a Form With a Factory

If a form needs some preparation for creation then a factory can be used.

The following example creates a class as factory for the form:

```php
final class AlbumFormFactory
{
    public function __invoke(Psr\Container\ContainerInterface): AlbumForm
    {
        $form = new AlbumForm();
        $form->setName('album');
        
        return $form;
    }
}
```

Register the form on the form element manager via the configuration key `form_elements` in the module configuration, e.g. `module/Album/config/module.config.php`:

<pre class="language-php" data-line="6-7"><code>
namespace Album;

return [
    'form_elements' => [
        'factories' => [
            // Add this line
            Form\AlbumForm::class => Form\AlbumFormFactory::class,
        ],
    ],
    // …
];
</code></pre>

Get the form and the name in a controller:

```php
namespace Album\Controller;

use Album\Form\AlbumForm;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;

use function assert;

final class AlbumController extends AbstractActionController
{
    // …
    
    public function addAction()
    {
        $form = $this->formElementManager->get(AlbumForm::class);
        assert($form instanceof AlbumForm);
        
        echo $form->getName(); // album
        
        // …
    }
}
```

Now the custom factory will be used to instance the form.

## Using a Custom Element without Registration

The form element manager [allows fetching custom elements without prior registration](../form-element-manager.md#usage-of-the-form-element-manager-in-a-form) with the manager.

The following example creates a custom element:

```php
final class ExampleElement extends Laminas\Form\Element
{
    // …
}
```

Create a form and add the custom element:

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

Fetch the form and the element in a controller:

```php
namespace Album\Controller;

use Album\Form\AlbumForm;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;

use function assert;

final class AlbumController extends AbstractActionController
{
    // …
    
    public function addAction()
    {
        $form = $this->formElementManager->get(AlbumForm::class);
        assert($form instanceof AlbumForm);
        
        echo $form->get('example')->getLabel(); // Example element
        
        // …
    }
}
```

## Register and Using a Custom Element with a Factory

If a custom element needs some preparation for creation then a factory can be used.

The following example creates a class as factory for the element of the previous example:

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

Register the element on the form element manager via the configuration key `form_elements` in the module configuration, e.g. `module/Album/config/module.config.php`:

<pre class="language-php" data-line="6-7"><code>
namespace Album;

return [
    'form_elements' => [
        'factories' => [
            // Add this line
            Form\ExampleElement::class => Form\ExampleElementFactory::class,
        ],
    ],
    // …
];
</code></pre>

Now the custom factory will be used to instance the element.

## Configuring the Form Element Manager

The [configuration of the form element manager follows the exact same pattern](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/) as for a normal service manager of laminas-servicemanager.

In a laminas-mvc based application this means to use the application or module configuration, like `config/autload/global.php` or `module/Album/config/module.config.php`, and the configuration key `form_elements`:

```php
return [
    'form_elements' => [
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
];
```

The factory `Laminas\Form\FormElementManagerFactory` uses the configuration, search for the configuration key `form_elements` and create the form element manager.

## Learn More

- [The basics of the form element manager](../form-element-manager.md)
- [Creating custom elements](../advanced.md#creating-custom-elements)
- [Handling dependencies](../advanced.md#handling-dependencies)
- [Configuring the service manager](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/)
