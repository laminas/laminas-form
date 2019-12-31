# Button

`Laminas\Form\Element\Button` represents a button form input.
It can be used with the `Laminas\Form\View\Helper\FormButton` view helper.

`Laminas\Form\Element\Button` extends from [`Laminas\Form\Element`](element.md).

## Basic Usage

This element automatically adds a `type` attribute of value `button`.

```php
use Laminas\Form\Element;
use Laminas\Form\Form;

$button = new Element\Button('my-button');
$button->setLabel('My Button');
$button->setValue('foo');

$form = new Form('my-form');
$form->add($button);
```
