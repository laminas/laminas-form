# Usage in a laminas-mvc Application

The following example shows _one_ potential use case of laminas-form within
a laminas-mvc based application. The example uses a module, a controller and the
laminas-form element manager.

The example is based on the [tutorial application](https://docs.laminas.dev/tutorials/getting-started/overview/)
which builds an album inventory system.

Before starting, make sure laminas-form is installed and configured.

## Create Form

Create a form as separate class, e.g. `module/Album/src/Form/AlbumForm.php`:

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
            'type'    => Text:class,
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
                'name'              => 'title',
                'filters'           => [
                    [
                        'name' => StripTags::class,
                    ],
                    [
                        'name' => StringTrim::class,
                    ],
                ],
                'validators'        => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 120,
                        ],
                    ],
                ],
            ],
            // …
        ];
    }
}
```

## Using Form

### Create Controller

Using the form in a controller, e.g.
`module/Album/Controller/AlbumController.php`:

```php
namespace Album\Controller;

use Album\Form\AlbumForm;
use Laminas\Form\FormInterface;
use Laminas\Mvc\Controller\AbstractActionController;

class AlbumController extends AbstractActionController
{
    /** @var FormInterface */
    private $form;
    
    public function __construct(AlbumForm $form)
    {
        $this->form = $form;
    }
    
    public function addAction()
    {
        // Set action attribute
        $this->form->setAttribute(
            'action',
            $this->url()->fromRoute('album', ['action' => 'add'])
        );

        $variables = ['form' => $this->form];
        
        if (! $this->getRequest()->isPost()) {
            return $variables;
        }

        // Validation
        $this->form->setData($this->getRequest()->getPost());
        if (! $this->form->isValid()) {
            return $variables;
        }
    
        // …

        return $this->redirect()->toRoute('album', ['action' => 'add']);
    }
}
```

### Create Factory for Controller

Fetch the `AlbumForm` from the form element manager in a factory,
e.g. `src/Album/Controller/AlbumControllerFactory.php`:

```php
namespace Album\Controller;

use Album\Form\AlbumForm;
use Laminas\ServiceManager\PluginManagerInterface;
use Psr\Container\ContainerInterface;

class AlbumControllerFactory
{
    public function __invoke(ContainerInterface $container) : AlbumController
    {
        /** @var PluginManagerInterface $formElementManager */
        $formElementManager = $container->get('FormElementManager');
        /** @var AlbumForm */ 
        $form = $formElementManager->get(AlbumForm::class);

        return new AlbumController($form);
    }
}
```

> ### Instantiating the Form
>
> The form element manager is used instead of directly instantiating the form to
> ensure to get the input filter manager injected. This allows usage of any
> input filter registered with the input filter managers which includes custom
> filters and validators.  
> Custom form elements can also be used in this way.
>
> Additionally the form element manager calls the `init` method _after_
> instantiating the form, ensuring all dependencies are fully injected
> first.

## Register Form and Controller

If no separate factory is required for the form, then the form element manager
will instantiating the form class. Otherwise the form must be registered.

To register the controller for the application, extend the configuration of the
module.  
Add the following lines to the module configuration file, e.g.
`module/Album/config/module.config.php`:

<pre class="language-php" data-line="6-7"><code>
namespace Album;

return [
    'controllers' => [
        'factories' => [
            // Add this line
            Controller\AlbumController::class => Controller\AlbumControllerFactory::class,
        ],
    ],
    // …
];
</code></pre>

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