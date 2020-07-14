# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.15.0 - 2020-07-14

### Added

- [#69](https://github.com/laminas/laminas-form/pull/69) adds support for the "minlength" attribute in the `FormTextarea` view helper.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.14.6 - 2020-06-22

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#68](https://github.com/laminas/laminas-form/pull/68) updates the `FormHidden` view helper to allow `autocomplete` as a valid attribute when rendering the element.

## 2.14.5 - 2020-03-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed `replace` version constraint in composer.json so repository can be used as replacement of `zendframework/zend-form:^2.14.3`.

## 2.14.4 - 2020-03-18

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#55](https://github.com/laminas/laminas-form/pull/55) fixes PHP 7.4 compatibility.

- [#55](https://github.com/laminas/laminas-form/pull/55) fixes accepting Traversable object in `Fieldset::populateValues`.

- [#60](https://github.com/laminas/laminas-form/pull/60) fixes accepting Traversable object in `Element\Collection::populateValues` and `Element\Collection::populateValues`.

- [#60](https://github.com/laminas/laminas-form/pull/60) fixes accepting Traversable object in `setOptions` of numerous form element classes.

## 2.14.3 - 2019-10-04

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#235](https://github.com/zendframework/zend-form/pull/235) fixes PHP 7.4 compatibility.

## 2.14.2 - 2019-10-03

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#232](https://github.com/zendframework/zend-form/pull/232) fixes validating `$creationOption`
  of `Laminas\Form\ElementFactory`. Allowed values are: array, Traversable or null.
  If invalid value provided exception will be thrown.

- [zendframework/zend-form#234](https://github.com/zendframework/zend-form/pull/234) registers `Search` and `Tel` form elements
  within `Laminas\Form\FormElementManager` plugin manager.

## 2.14.1 - 2019-02-26

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#230](https://github.com/zendframework/zend-form/pull/230) fixes the "`__clone` method called on non-object" error that happens when
  the `$targetElement` is `null` within a `Collection` instance. It now properly
  sets the data to an empty array in such circumstances.

## 2.14.0 - 2019-01-07

### Added

- [zendframework/zend-form#228](https://github.com/zendframework/zend-form/pull/228) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-form#228](https://github.com/zendframework/zend-form/pull/228) removes support for laminas-stdlib v2 releases.

### Fixed

- Nothing.

## 2.13.0 - 2018-12-11

### Added

- [zendframework/zend-form#224](https://github.com/zendframework/zend-form/pull/224) adds support for laminas-hydrator v3 releases, while keeping support for
  versions 1 and 2.

- [zendframework/zend-form#211](https://github.com/zendframework/zend-form/pull/211) adds support for the HTML5 `minlength` attribute in all form elements
  that support it.

- [zendframework/zend-form#217](https://github.com/zendframework/zend-form/pull/217) adds `Laminas\Form\View\HelperTrait`, which can be used to provide IDE
  autocompletion for view helpers provided by laminas-form. See
  https://docs.laminas.dev/laminas-form/view-helpers/#ide-auto-completion-in-templates
  for more information.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.12.1 - 2018-12-11

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#218](https://github.com/zendframework/zend-form/pull/218) ensures object values of select elements can be rendered without error.

- [zendframework/zend-form#216](https://github.com/zendframework/zend-form/pull/216) fixes an issue when performing data binding and a fieldset has no mapped
  input elements, casting `null` values to empty arrays to ensure they can be
  passed to an input filter.

- [zendframework/zend-form#207](https://github.com/zendframework/zend-form/pull/207) fixes the return value annotation for the `Fieldset::get()` method to
  indicate it can also return a `FieldsetInterface` instance.

## 2.12.0 - 2018-05-16

### Added

- [zendframework/zend-form#194](https://github.com/zendframework/zend-form/pull/194) adds the ability to whitelist additional HTML attributes for use with a view helper,
  as well as attribute prefixes. These can be enabled via the following:
  
  ```php
  $helper->addValidAttribute('attribute-name');
  $helper->addValidAttributePrefix('prefix-');
  ```

- [zendframework/zend-form#188](https://github.com/zendframework/zend-form/pull/188) adds a new method to the `FormElementErrors` view helper, `setTranslateMessages(bool $flag)`.
  By default, the helper continues to translate error messages (if a translator
  is present), as introduced in 2.11.0. However, using this method, you can
  disable translation, which may be necessary to prevent double translation
  and/or to reduce logs from missed translation lookups. Because the method
  implements a fluent interface, you may do so in one line:
  
  ```php
  echo $this->formElementErrors()->setTranslateMessages(false);
  ```
  
  Note: you will need to reset the value afterwards if you want translations to occur
  in later invocations.

### Changed

- [zendframework/zend-form#193](https://github.com/zendframework/zend-form/pull/193) modifies how attributes are escaped. If laminas-espaper raises an exception
  for an invalid attribute value, helpers will now catch the exception, and use
  a blank value for the attribute. This prevents 500 errors from being raised
  for such pages.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.11.0 - 2017-12-06

### Added

- [zendframework/zend-form#104](https://github.com/zendframework/zend-form/pull/104) adds the ability
  for the `FormElementErrors` view helper to translate validation error messages
  using the composed translator and text domain instances.

- [zendframework/zend-form#171](https://github.com/zendframework/zend-form/pull/171),
  [zendframework/zend-form#186](https://github.com/zendframework/zend-form/pull/186), and
  [zendframework/zend-form#187](https://github.com/zendframework/zend-form/pull/187) add support for
  PHP 7.2.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-form#171](https://github.com/zendframework/zend-form/pull/171) removes support
  for HHVM.

- [zendframework/zend-form#186](https://github.com/zendframework/zend-form/pull/186) removes support
  for PHP 5.5.

### Fixed

- [zendframework/zend-form#162](https://github.com/zendframework/zend-form/pull/162) fixes an issue
  with hydration when a form has called `setWrapElements(true)`, ensuring that
  binding values in a fieldset will correctly identify the elements in the
  provided data.

- [zendframework/zend-form#172](https://github.com/zendframework/zend-form/pull/172) fixes the
  `DateTime` element such that it no longer attempts to use its
  `DATETIME_FORMAT` constant, but, rather, the value of the `$format` property,
  when representing the element; this change allows developers to override the
  format, which was the original intention.

- [zendframework/zend-form#178](https://github.com/zendframework/zend-form/pull/178) loosens the checks
  in `Laminas\Form\Element\DateTime::getValue()` to check against PHP's `DateTimeInterface` (vs
  `DateTime`) when retrieving the value; this fixes edge cases where it was
  instead returning the format for `DateTimeImmutable` values.

## 2.10.2 - 2017-05-18

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#161](https://github.com/zendframework/zend-form/pull/161) adds an import
  statement to the `ElementFactory`, fixing an error whereby checks for
  `Traversable` creation options would lead to a service creation exception;
  these now correctly identify traversable options and convert them to an array.
- [zendframework/zend-form#164](https://github.com/zendframework/zend-form/pull/164) fixes how the
  `FormElementManagerFactory` factory initializes the plugin manager instance,
  ensuring it is injecting the relevant configuration from the `config` service
  and thus seeding it with configured form/form element services.  This means
  that the `form_elements` configuration will now be honored in non-laminas-mvc
  contexts.
- [zendframework/zend-form#159](https://github.com/zendframework/zend-form/pull/159) fixes the behavior
  of the `min` and `max` attributes of the various `DateTime` elements, ensuring
  that the elements raise an exception during instantiation if the values
  provided are in a format that `DateTime` does not recognize for the element
  type in question.

## 2.10.1 - 2017-04-26

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#134](https://github.com/zendframework/zend-form/pull/134) fixes how the
  `FormElementManager` handles invokable classes when the `autoAddInvokableClass`
  flag is enabled. Previously, it used the built-in utilities from
  laminas-servicemanager, but now correctly uses its own `setInvokableClass()`
  method, which forces usage of the `ElementFactory` for such classes, and thus
  ensures the name and options are passed to the element constructor.
- [zendframework/zend-form#136](https://github.com/zendframework/zend-form/pull/136) fixes how error
  messages are provided when an element uses a required `ArrayInput`, but no
  values are submitted. Previously, no messages were returned; now they are.
- [zendframework/zend-form#156](https://github.com/zendframework/zend-form/pull/156) fixes how elements
  that act as `InputProvider`s are merged into parent `CollectionInputFilter`s;
  previously, forms did not check if the element was in the target input filter
  composed in a `CollectionInputFilter`, leading to duplicate elements with
  varying behavior; now the inputs are correctly merged.

## 2.10.0 - 2017-02-23

### Added

- [zendframework/zend-form#115](https://github.com/zendframework/zend-form/pull/115) adds translatable
  HTML attributes to the abstract view helper.
- [zendframework/zend-form#116](https://github.com/zendframework/zend-form/pull/116) adds the InputFilterFactory
  dependency to the constructor.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#139](https://github.com/zendframework/zend-form/pull/139) adds support for 
  ReCaptcha version 2 though laminas-captcha 2.7.1.

## 2.10.0 - 2017-02-23

### Added

- [zendframework/zend-form#115](https://github.com/zendframework/zend-form/pull/115) adds translatable
  HTML attributes to the abstract view helper.
- [zendframework/zend-form#116](https://github.com/zendframework/zend-form/pull/116) adds the InputFilterFactory
  dependency to the constructor.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#139](https://github.com/zendframework/zend-form/pull/139) adds support for 
  ReCaptcha version 2 though laminas-captcha 2.7.1.

## 2.9.2 - 2016-09-22

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#122](https://github.com/zendframework/zend-form/pull/122) fixes collection
  binding following successful validation. The fix introduced in zendframework/zend-form#106, while it
  corrected the behavior around binding a collection that was not re-submitted,
  broke behavior around binding submitted collections. zendframework/zend-form#122 corrects the issue,
  retaining the fix from zendframework/zend-form#106.

## 2.9.1 - 2016-09-14

### Added

- [zendframework/zend-form#85](https://github.com/zendframework/zend-form/pull/85) adds support for the
  laminas-code 3.0 series (retaining support for the 2.* series).

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#119](https://github.com/zendframework/zend-form/pull/119) fixes the order in
  which the default initializers are injected into the `FormElementManager`,
  ensuring that the initializer injecting a factory into a `FormFactoryAware`
  instance is triggered before the initializer that calls `init()`, and also
  that the initializer calling `init()` is always triggered last.
- [zendframework/zend-form#106](https://github.com/zendframework/zend-form/pull/106) updates behavior
  around binding collection values to a fieldset or form such that if the
  collection is not part of the current validation group, its value will not be
  overwritten with an empty set.

## 2.9.0 - 2016-06-07

### Added

- [zendframework/zend-form#57](https://github.com/zendframework/zend-form/pull/57) adds new elements,
  `FormSearch` and `FormTel`, which map to the `FormSearch` and `FormTel` view
  helpers.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updates the composer suggestions list to remove those that were redundant, and
  to add explicit constraints and reasons for each listed (e.g., laminas-code is
  required for annotations support).

## 2.8.4 - 2016-06-07

### Added

- [zendframework/zend-form#74](https://github.com/zendframework/zend-form/pull/74) adds an 
  alias for the `FormTextarea` view helper that is referenced in the
  documentation.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#77](https://github.com/zendframework/zend-form/pull/77) updates
  `Laminas\Form\View\HelperConfig` to improve performance when running under
  laminas-servicemanager v3.
- [zendframework/zend-form#19](https://github.com/zendframework/zend-form/pull/19) provides a thorough
  fix for an issue when removing all items in a collection associated with a
  form. Prior to this release, values that existed in the collection persisted
  when a form submission intended to remove them.

## 2.8.3 - 2016-05-03

### Added

- [zendframework/zend-form#70](https://github.com/zendframework/zend-form/pull/70) adds and publishes
  the documentation to https://docs.laminas.dev/laminas-form/

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#69](https://github.com/zendframework/zend-form/pull/69) fixes aliases in the
  `FormElementManager` polyfill for laminas-servicemanager v2 to ensure they are
  canonicalized correctly.

## 2.8.2 - 2016-05-01

### Added

- [zendframework/zend-form#60](https://github.com/zendframework/zend-form/pull/60) adds an alias from
  `Laminas\Form\FormElementManager` to `FormElementManager` in the `ConfigProvider`.
- [zendframework/zend-form#67](https://github.com/zendframework/zend-form/pull/67) adds polyfills for
  the `FormElementManager` to vary its definitions based on the major version of
  laminas-servicemanager in use. `FormElementManagerFactory` was updated to return
  the specific polyfill version, and an autoload rule was added to alias the
  class to the correct polyfill version. The polyfills were necessary to ensure
  that invokable classes are mapped to the new `ElementFactory` introduced in
  the 2.7 series, thus ensuring instantiation is performed correctly.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#65](https://github.com/zendframework/zend-form/pull/65) fixes instantiation
  of `Laminas\Form\FormElementManager` to ensure that the default initializers,
  `injectFactory()` and `callElementInit()` are registered as the first and last
  initializers, respectively, during construction, restoring the pre-2.7
  behavior.
- [zendframework/zend-form#67](https://github.com/zendframework/zend-form/pull/67) fixes the behavior
  of `Factory::create()` to the pre-2.7.1 behavior of *not* passing creation
  options when retrieving an instance from the `FormElementManager`. This
  ensures that options are not passed to Element/Fieldset/Form instances
  until after they are fully initialized, ensuring that all dependencies are
  present.

## 2.8.1 - 2016-04-18

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#59](https://github.com/zendframework/zend-form/pull/59) fixes the
  `Module::init()` method to properly receive a `ModuleManager` instance, and
  not expect a `ModuleEvent`.

## 2.8.0 - 2016-04-07

### Added

- [zendframework/zend-form#53](https://github.com/zendframework/zend-form/pull/53) adds
  `Laminas\Form\FormElementManagerFactory`, for creating and returning instances of
  `Laminas\Form\FormElementManager`. This factory was ported from laminas-mvc, and
  will replace it for version 3 of that component.
- [zendframework/zend-form#53](https://github.com/zendframework/zend-form/pull/53) adds
  `Laminas\Form\Annotation\AnnotationBuilderFactory`, for creating and returning
  instances of `Laminas\Form\Annotation\AnnotationBuilder`. This factory was ported
  from laminas-mvc, and will replace it for version 3 of that component.
- [zendframework/zend-form#53](https://github.com/zendframework/zend-form/pull/53) exposes the package
  as a config-provider and Laminas component, by adding:
  - `ConfigProvider`, which maps the `FormElementsManager` and
    `FormAnnotationBuilder` servies previously provided by laminas-mvc; the form
    abstract factory as previously registered by laminas-mvc; and all view helper
    configuration.
  - `Module`, which maps services and view helpers per the `ConfigProvider`, and
    provides configuration to the laminas-modulemanager `ServiceLocator` in order
    for modules to provide form and form element configuration.

### Deprecated

- [zendframework/zend-form#53](https://github.com/zendframework/zend-form/pull/53) deprecates
  `Laminas\Form\View\HelperConfig`; the functionality is made obsolete by
  `ConfigProvider`. It now consumes the latter in order to provide view helper
  configuration.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.7.1 - 2016-04-07

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#24](https://github.com/zendframework/zend-form/pull/24) ensures that when
  `Laminas\Form\Form::getInputFilter()` when lazy-creates an `InputFilter`
  instance, it is populated with the `InputFilterFactory` present in its own
  `FormFactory`. This ensures that any custom inputs, input filters, validators,
  or filters are available to the new instance.
- [zendframework/zend-form#38](https://github.com/zendframework/zend-form/pull/38) removes the
  arbitrary restriction of only the "labelledby" and "describedby" aria
  attributes on form element view helpers; any aria attribute is now allowed.
- [zendframework/zend-form#45](https://github.com/zendframework/zend-form/pull/45) fixes the behavior
  in `Laminas\Form\Factory::create()` when pulling elements from the form element
  manager; it now will pass specifications provided for the given element when
  calling the manager's `get()` method.

## 2.7.0 - 2016-02-22

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#40](https://github.com/zendframework/zend-form/pull/40) and
  [zendframework/zend-form#43](https://github.com/zendframework/zend-form/pull/43) prepare the
  component to be forwards compatible with each of the following:
  - laminas-eventmanager v3
  - laminas-hydrator v2.1
  - laminas-servicemanager v3
  - laminas-stdlib v3
- [zendframework/zend-form#14](https://github.com/zendframework/zend-form/pull/14) ensures that
  collections can remove all elements when populating values.

## 2.6.0 - 2015-09-22

### Added

- [zendframework/zend-form#17](https://github.com/zendframework/zend-form/pull/17) updates the component
  to use laminas-hydrator for hydrator functionality; this provides forward
  compatibility with laminas-hydrator, and backwards compatibility with
  hydrators from older versions of laminas-stdlib.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.5.3 - 2015-09-22

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#16](https://github.com/zendframework/zend-form/pull/16) updates the
  laminas-stdlib dependency to reference `>=2.5.0,<2.7.0` to ensure hydrators
  will work as expected following extraction of hydrators to the laminas-hydrator
  repository.

## 2.5.2 - 2015-09-09

### Added

- Nothing.

### Deprecated

- [zendframework/zend-form#12](https://github.com/zendframework/zend-form/pull/12) deprecates the
  `AllowEmpty` and `ContinueIfEmpty` annotations, to mirror changes made in
  [laminas-inputfilter#26](https://github.com/zendframework/zend-inputfilter/pull/26).

### Removed

- Nothing.

### Fixed

- [zendframework/zend-form#1](https://github.com/zendframework/zend-form/pull/1) `AbstractHelper` was
  being utilized on the method signature vs. `HelperInterface`.
- [zendframework/zend-form#9](https://github.com/zendframework/zend-form/pull/9) fixes typos in two
  `aria` attribute names in the `AbstractHelper`.
