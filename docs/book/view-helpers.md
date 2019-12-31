# Form View Helpers

## Introduction

Laminas comes with an initial set of helper classes related to Forms: e.g., rendering a text
input, selection box, or form labels. You can use helper, or plugin, classes to perform these
behaviors for you.

See the section on \[view helpers\](laminas.view.helpers) for more information.

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
> ```php
> /**
>  * @var Laminas\View\Renderer\PhpRenderer|Laminas\Form\View\HelperTrait $this
>  */
> ```
>
> You may chain as many `HelperTrait` traits as you like, depending on view
> helpers from which Laminas component you are using and would like to
> provide auto-completion for.

## Standard Helpers

## HTML5 Helpers

## File Upload Progress Helpers
