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

use Laminas\Form\Element\Text;
use Laminas\Form\Form;

class AlbumForm extends Form
{
    public function init()
    {
        // Title
        $this->add(
            [
                'name'    => 'title',
                'type'    => Text:class,
                'options' => [
                    'label' => 'Title',
                ],
            ]
        );
    
        // …
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
    
    public function __construct(FormInterface $form)
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
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\PluginManagerInterface;

class AlbumControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
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

Extend the configuration of the module to register the form and controller in
the application.  
Add the following lines to the module configuration file, e.g.
`module/Album/config/module.config.php`:

<pre class="language-php" data-line="8-9,12-17"><code>
namespace Album;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            // Add this line
            Controller\AlbumController::class => Controller\AlbumControllerFactory::class,
        ],
    ],
    // Add the following array
    'form_elements' => [
        'factories => [
            Form\AlbumForm::class => InvokableFactory::class,
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