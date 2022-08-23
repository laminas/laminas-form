# Usage in a laminas-mvc Application

The following example shows _one_ potential use case of laminas-form within
a laminas-mvc based application. The example uses a module, a controller and the
laminas-form element manager.

The example is based on the [tutorial application](https://docs.laminas.dev/tutorials/getting-started/overview/)
which builds an album inventory system.

Before starting, make sure laminas-form is [installed and configured](../installation.md).

## Create Form

[Create a form as separate class](../quick-start.md#factory-backed-form-extension)
using the [`init` method](../advanced.md#initialization), e.g.
`module/Album/src/Form/AlbumForm.php`:

```php
namespace Album\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;

class AlbumForm extends Form implements InputFilterProviderInterface
{
    public function init() : void
    {
        // Title
        $this->add([
            'name'    => 'title',
            'type'    => Text::class,
            'options' => [
                'label' => 'Title',
            ],
        ]);

        // …
    }

    public function getInputFilterSpecification() : array
    {
        return [
            // Title
            [
                'name'    => 'title',
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 100,
                        ],
                    ],
                ],
            ],
            // …
        ];
    }
}
```

## Create Controller

[Create a controller class](https://docs.laminas.dev/laminas-mvc/quick-start/#create-a-controller) and inject the form element manager via the constructor, e.g. `module/Album/Controller/AlbumController.php`:

```php
namespace Album\Controller;

use Album\Form\AlbumForm;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;

class AlbumController extends AbstractActionController
{
    public function __construct(
        public readonly FormElementManager $formElementManager
    ) {}
    
    public function addAction()
    {
        $form = $this->formElementManager->get(AlbumForm::class);
    
        // Set action attribute
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('album', ['action' => 'add'])
        );

        $variables = ['form' => $form];
        
        if (! $this->getRequest()->isPost()) {
            return $variables;
        }

        // Validation
        $form->setData($this->getRequest()->getPost());
        if (! $form->isValid()) {
            return $variables;
        }
    
        // …

        return $this->redirect()->toRoute('album', ['action' => 'add']);
    }
}
```

> ### Instantiating the Form
>
> The form element manager is used instead of directly instantiating the form to
> ensure to get the input filter manager injected. This allows usage of any
> input filter registered with the input filter managers which includes custom
> filters and validators.  
> [Custom form elements](../advanced.md#creating-custom-elements)
> can also be used in this way.
>
> Additionally, the [form element manager calls the `init` method](../advanced.md#initialization)
> _after_ instantiating the form, ensuring all dependencies are fully injected
> first.

## Register Form and Controller

If no separate factory is required for the form, then the form element manager will be instantiating the form class without prior registration. Otherwise, the form must be registered.

To [register the controller](https://docs.laminas.dev/laminas-mvc/quick-start/#create-a-route)
for the application, extend the configuration of the module.  
Add the following lines to the module configuration file, e.g.
`module/Album/config/module.config.php`:

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

The example uses the [reflection factory from laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/reflection-abstract-factory/) to resolve the constructor dependencies for the controller class.

## Create View Script

Create a view script in the module, e.g.
`module/Album/view/album/album/add.phtml`:

```php
<?php
/**
 * @var Laminas\View\Renderer\PhpRenderer|Laminas\Form\View\HelperTrait $this
 * @var Laminas\Form\Form                                               $form
 */
$this->headTitle('Add new album');
?>

<h1>Add new album</h1>

<?= $this->form($form) ?>
```

The [`Form` view helper](../helper/form.md) is used to render all HTML for the
form.
