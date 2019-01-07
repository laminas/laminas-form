# View Helpers

zend-form comes with an initial set of zend-view helper classes for tasks such
as rendering forms, rendering a text input, rendering a selection box, etc.

See the [view helpers documentation](https://docs.zendframework.com/zend-view/helpers/intro/)
for more information.

> ### IDE auto-completion in templates
>
> The `Zend\Form\View\HelperTrait` trait can be used to provide auto-completion
> for modern IDEs. It defines the aliases of the view helpers in a DocBlock as
> `@method` tags.
>
> #### Usage
>
> In order to allow auto-completion in templates, `$this` variable should be
> type-hinted via a DocBlock at the top of your template. It is recommended that
> you always add the `Zend\View\Renderer\PhpRenderer` as the first type, so that
> the IDE can auto-suggest the default view helpers from `zend-view`. Next,
> chain the `HelperTrait` from `zend-form` with a pipe symbol (a.k.a. vertical
> bar) `|`:
>
> ```php
> /**
>  * @var Zend\View\Renderer\PhpRenderer|Zend\Form\View\HelperTrait $this
>  */
> ```
>
> You may chain as many `HelperTrait` traits as you like, depending on view
> helpers from which Zend Framework component you are using and would like to
> provide auto-completion for.
