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

Method signature                       | Description
-------------------------------------- | -----------
`setDoctype(string $doctype) : void`   | Sets a doctype to use in the helper.
`getDoctype() : string`                | Returns the doctype used in the helper.
`setEncoding(string $encoding) : void` | Set the translation text domain to use in helper when translating.
`getEncoding() : string`               | Returns the character encoding used in the helper.
`getId() : string or null`             | Returns the element id. If no ID attribute present, attempts to use the name attribute. If name attribute is also not present, returns `null`.
