# AbstractHelper

The `AbstractHelper` is used as a base abstract class for zend-form view
helpers, providing methods for validating form HTML attributes, as well as
controlling the doctype and character encoding. `AbstractHelper` also extends
from `Zend\I18n\View\Helper\AbstractTranslatorHelper` which provides an
implementation for the `Zend\I18n\Translator\TranslatorAwareInterface` that
allows setting a translator and text domain.

## Public methods

The following public methods are in addition to the inherited methods of
[Zend\I18n\View\Helper\AbstractTranslatorHelper](http://zendframework.github.io/zend-i18n/view-helpers/#abstract-translator-helper).

Method signature                                               | Description
-------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------
`setDoctype(string $doctype) : void`                           | Sets a doctype to use in the helper.
`getDoctype() : string`                                        | Returns the doctype used in the helper.
`setEncoding(string $encoding) : void`                         | Set the translation text domain to use in helper when translating.
`getEncoding() : string`                                       | Returns the character encoding used in the helper.
`getId() : string or null`                                     | Returns the element id. If no ID attribute present, attempts to use the name attribute. If name attribute is also not present, returns `null`.
`addTranslatableAttribute(string $attribute) : self`           | Marks the given HTML attribute as translatable.
`addTranslatableAttributePrefix(string $prefix) : self`        | Marks all HTML attributes that start with the given prefix as translatable.
`addTranslatableAttribute(string $attribute) : self`           | Marks the given HTML attribute as translatable.
`addTranslatableAttributePrefix(string $prefix) : self`        | Marks all HTML attributes that start with the given prefix as translatable.
`addDefaultTranslatableAttribute(string $attribute) : void`    | Marks the given HTML attribute as translatable for all view helpers.
`addDefaultTranslatableAttributePrefix(string $prefix) : void` | Marks all HTML attributes that start with the given prefix as translatable for all view helpers.

## Translation

Attaching a translator and setting a text domain (using the formLabel view helper as an example):

```php
// Setting a translator
$this->formLabel()->setTranslator($translator);

// Setting a text domain
$this->formLabel()->setTranslatorTextDomain('my-text-domain');

// Setting both
$this->formLabel()->setTranslator($translator, 'my-text-domain');
```

> ### Enabling translation
>
> If you have a translator in your application container under either the key,
> `translator` or `MvcTranslator`, the view helper plugin manager will
> automatically attach the translator to the view helpers.

### What will be translated?

The specific view helpers are responsible to determine what exactly should be translated 
(e. g. the Label in the FormLabel view helper or the "title" HTML attribute).

If you want to have certain HTML attribute values translated you can mark them as "translatable":
```php
// mark one attribute as translatable
$this->formLabel()->addTranslatableAttribute('data-translate-me');

// mark an prefix as translatable
$this->formLabel()->addTranslatableAttributePrefix('data-translatable-');
```

Or you can mark them as translatable for all view helpers (e. g. the title attribute):
```php
// mark one attribute as translatable
\Zend\Form\View\Helper\AbstractHelper->addDefaultTranslatableAttribute('title');

// mark an prefix as translatable
\Zend\Form\View\Helper\AbstractHelper->addDefaultTranslatableAttributePrefix('data-translatable-');
```
