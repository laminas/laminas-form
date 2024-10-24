# Collection

Sometimes, you may want to add an input (or a set of inputs) multiple times,
either because you don't want to duplicate code, or because you do not know in
advance how many elements you will need (in the case of elements dynamically
added to a form using Javascript, for instance). For more information about
collections, please refer to the [form Collections tutorial](../collections.md).

`Laminas\Form\Element\Collection` is meant to be paired with the
`Laminas\Form\View\Helper\FormCollection`.

## Basic Usage

```php
use Laminas\Form\Element;
use Laminas\Form\Form;

$colors = new Element\Collection('collection');
$colors->setLabel('Colors');
$colors->setCount(2);
$colors->setTargetElement(new Element\Color());
$colors->setShouldCreateTemplate(true);

$form = new Form('my-form');
$form->add($colors);
```

Using array notation:

```php
use Laminas\Form\Element;
use Laminas\Form\Form;

$form = new Form('my-form');
$form->add([
    'type' => Element\Collection::class,
    'options' => [
        'label' => 'Colors',
        'count' => 2,
        'should_create_template' => true,
        'target_element' => new Element\Color()
    ],
]);
```

## Public Methods

The following methods are specific to the `Collection` element; all other methods
defined by the [parent `Element` class](element.md#public-methods) are also
available.

| Method signature                                                       | Description                                                                                                                                                                                                                                                                                                                                                                                                                                 |
|------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `setOptions(array $options) : void`                                    | Set options for an element of type Collection. Accepted options, in addition to the options inherited from [Element](element.md#public-methods), are: `target_element`, `count`, `allow_add`, `allow_remove`, `should_create_template` and `template_placeholder`. Those option keys respectively call `setTargetElement()`, `setCount()`, `setAllowAdd()`, `setAllowRemove()`, `setShouldCreateTemplate()` and `setTemplatePlaceholder()`. |
| `allowObjectBinding(object $object) : bool`                            | Checks if the object can be set in this fieldset.                                                                                                                                                                                                                                                                                                                                                                                           |
| `setObject(array \| Traversable $object) : void`                       | Set the object used by the hydrator. In this case the "object" is a collection of objects.                                                                                                                                                                                                                                                                                                                                                  |
| `populateValues(array \| Traversable $data) : void`                    | Populate values                                                                                                                                                                                                                                                                                                                                                                                                                             |
| `allowValueBinding() : bool`                                           | Checks if this fieldset can bind data                                                                                                                                                                                                                                                                                                                                                                                                       |
| `setCount($count) : void`                                              | Defines how many times the target element will be initially rendered by the `Laminas\Form\View\Helper\FormCollection` view helper.                                                                                                                                                                                                                                                                                                          |
| `getCount() : integer`                                                 | Return the number of times the target element will be initially rendered by the `Laminas\Form\View\Helper\FormCollection` view helper.                                                                                                                                                                                                                                                                                                      |
| `setTargetElement($elementOrFieldset) : void`                          | This function either takes an `Laminas\Form\ElementInterface`, `Laminas\Form\FieldsetInterface` instance or an array to pass to the form factory. When the Collection element will be validated, the input filter will be retrieved from this target element and be used to validate each element in the collection.                                                                                                                        |
| `getTargetElement() : ElementInterface \| null`                        | Return the target element used by the collection.                                                                                                                                                                                                                                                                                                                                                                                           |
| `setAllowAdd($allowAdd) : void`                                        | If allowAdd is set to true (which is the default), new elements added dynamically in the form (using JavaScript, for instance) will also be validated and retrieved.                                                                                                                                                                                                                                                                        |
| `allowAdd() : boolean`                                                 | Return if new elements can be dynamically added in the collection.                                                                                                                                                                                                                                                                                                                                                                          |
| `setAllowRemove($allowRemove) : void`                                  | If allowRemove is set to true (which is the default), new elements added dynamically in the form (using JavaScript, for instance) will be allowed to be removed.                                                                                                                                                                                                                                                                            |
| `allowRemove() : boolean`                                              | Return if new elements can be dynamically removed from the collection.                                                                                                                                                                                                                                                                                                                                                                      |
| `setShouldCreateTemplate($shouldCreateTemplate) : void`                | If shouldCreateTemplate is set to `true` (defaults to `false`), a `<span>` element will be generated by the `Laminas\Form\View\Helper\FormCollection` view helper. This non-semantic `span` element contains a single data-template HTML5 attribute whose value is the whole HTML to copy to create a new element in the form. The template is indexed using the `templatePlaceholder` value.                                               |
| `shouldCreateTemplate() : boolean`                                     | Return if a template should be created.                                                                                                                                                                                                                                                                                                                                                                                                     |
| `setTemplatePlaceholder($templatePlaceholder) : void`                  | Set the template placeholder (defaults to `__index__`) used to index element in the template.                                                                                                                                                                                                                                                                                                                                               |
| `getTemplatePlaceholder() : string`                                    | Returns the template placeholder used to index element in the template.                                                                                                                                                                                                                                                                                                                                                                     |
| `getTemplateElement() : null \| ElementInterface \| FieldsetInterface` | Get a template element used for rendering purposes only                                                                                                                                                                                                                                                                                                                                                                                     |
| `prepareElement : void`                                                | Prepare the collection by adding a dummy template element if the user want one                                                                                                                                                                                                                                                                                                                                                              |
| `prepareFieldset() : void`                                             | If both count and targetElement are set, add them to the fieldset                                                                                                                                                                                                                                                                                                                                                                           |
