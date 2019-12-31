# View Helpers

laminas-form comes with an initial set of laminas-view helper classes for tasks such
as rendering forms, rendering a text input, rendering a selection box, etc.

See the [view helpers documentation](https://docs.laminas.dev/laminas-view/helpers/intro/)
for more information.

> ### IDE auto-completion in templates
>
> The `Laminas\Form\View\HelperTrait` trait can be used to provide auto-completion
> for modern IDEs. It defines the aliases of the view helpers in a DocBlock as
> `@method` tags.
>
> #### Usage
>
> In order to allow auto-completion in templates, `$this` variable should be
> type-hinted via a DocBlock at the top of your template. It is recommended that
> you always add the `Laminas\View\Renderer\PhpRenderer` as the first type, so that
> the IDE can auto-suggest the default view helpers from `laminas-view`. Next,
> chain the `HelperTrait` from `laminas-form` with a pipe symbol (a.k.a. vertical
> bar) `|`:
>
> ```php
> /**
>  * @var Laminas\View\Renderer\PhpRenderer|Laminas\Form\View\HelperTrait $this
>  */
> ```
>
> You may chain as many `HelperTrait` traits as you like, depending on view
> helpers from which Laminas component you are using and would like to
> provide auto-completion for.
