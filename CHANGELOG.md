# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

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
