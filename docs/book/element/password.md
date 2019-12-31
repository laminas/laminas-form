# Password

`Laminas\Form\Element\Password` represents a password form input.
It can be used with the [FormPassword](../helper/form-password.md) view helper.

## Basic Usage

This element automatically adds a `type` attribute of value `password`.

```php
use Laminas\Form\Element;
use Laminas\Form\Form;

$password = new Element\Password('my-password');
$password->setLabel('Enter your password');

$form = new Form('my-form');
$form->add($password);
```
