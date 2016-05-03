# FormCollection

`Zend\Form\View\Helper\FormCollection` was originally developed for rendering
`Zend\Form\Element\Collection` instances, but is capable of rendering any
iterable element type, including fieldsets and forms.

Internally, it iterates the element provided, and calls one of the following
view helpers based on the element encountered:

- If a fieldset (or, by extension, collection or form) is encountered, it is
  passed to itself (in other words, `FormCollection`).
- Otherwise, it is passed to [FormRow](form-row.md).

## Basic usage

```php
use My\ContactFieldset;
use Zend\Form\Element;

$collection = new Element\Collection();
$collection->setName('contacts');
$collection->setShouldCreateTemplate(true);
$collection->setAllowAdd(true);
$collection->setTargetElement([ 'type' => ContactFieldset::class ]);

// In a view script:
$this->formCollection($collection);
```

The above, assuming the fieldset is created correctly, will generate one or more
fieldsets with the name `contacts[]`, each containing the elements defined in
`My\ContactFieldset`. The number of fieldsets created will be based on what data
was bound to the form.
