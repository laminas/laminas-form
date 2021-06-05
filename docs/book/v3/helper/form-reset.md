# FormReset

The `FormReset` view helper can be used to render an `<input type="reset">` HTML
form input.

## Basic usage

```php
use Laminas\Form\Element;

$element = new Element('my-reset');
$element->setAttribute('value', 'Reset');

// Within your view...
echo $this->formReset($element);
```

Output:

```html
<input type="reset" name="my-reset" value="Reset">
```
