# Migration from Version 2 to 3

laminas-form version 3 makes a number of changes that may affect your application.
This document details those changes, and provides suggestions on how to update your application to work with version 3.

## New Features

### Support for PHP8 Attributes

laminas-form version 3 supports native PHP8 attributes as an alternative to DocBlock annotations.
While for new projects it is recommended to use PHP8 attributes, there is no need to migrate DocBlock annotations to PHP8 attributes, as laminas-form will continue to support DocBlock annotations.

In case you still want to migrate from DocBlock annotations to PHP8 attributes, please have a look at the [annotations chapter](../form-creation/attributes-or-annotations.md), which provides a side-by-side comparison of these annotations.

## Signature Changes

### Native Types

laminas-form version 3 promoted `@param` and `@return` annotations of non-mixed types to the corresponding function signatures.
This change should affect only highly customized element, fieldset or form classes.
If you need to automate native types promotion, [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) can help you with this command:

```console
$ php-cs-fixer fix --rules=phpdoc_to_param_type,phpdoc_to_return_type --allow-risky=yes path/to/my/custom/forms/
```

### API Changes

Many notable API have been changed:

- `\Laminas\Form\FormInterface::setValidationGroup(FormInterface::VALIDATE_ALL)` has now its own dedicated method `\Laminas\Form\FormInterface::setValidateAll()`

## New Dependencies

### Using doctrine/annotations Now Instead of laminas/laminas-code

Since laminas-code dropped support for annotation parsing with version 4, laminas-form switched to [doctrine/annotations](https://www.doctrine-project.org/projects/annotations.html) for [annotation parsing](../form-creation/attributes-or-annotations.md#using-docblock-annotations).
For most users, this will not have any side effects, however, you must ensure that you install doctrine/annotations with at least version 1.12.0:

```console
$ composer require doctrine/annotations
```

## Deprecations

### `Element\DateTime` is Deprecated

Since the HTML element `<input type="datetime">` has been [removed from WHATWG HTML](https://github.com/whatwg/html/issues/336) and it currently is not supported by any major browsers (see [MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/datetime)), its usage in form of [`Element\DateTime`](../element/date-time.md) and [`View\Helper\FormDateTime`](../helper/form-date-time.md) is deprecated.

Please use something else like [`DateTimeLocal`](../element/date-time-local.md) or `DateTimeSelect`.

### Single Associative Array in Constructors of Annotations Is Deprecated

After upgrading you may receive one or more deprecation notices about passing a single array to the constructor of annotation classes.
While the old style will continue to work, it is recommended to update your annotations.

Please see the following list of potential notices and how to eliminate them:

#### ComposedObject

Passing a single array to the constructor of [`Laminas\Form\Annotation\ComposedObject`](../form-creation/attributes-or-annotations.md#composedobject) is deprecated since 3.0.0, please use separate parameters.

Old style:

```php
/**
 * @ComposedObject({"target_object": "Namespace\Model\Entity", "is_collection": "true", "options": {}})
 */
```

New style:

```php
/**
 * @ComposedObject("Namespace\Model\Entity", isCollection="true", options={})
 */
```

#### Filter

Passing a single array to the constructor of [`Laminas\Form\Annotation\Filter`](../form-creation/attributes-or-annotations.md#filter) is deprecated since 3.0.0, please use separate parameters.

Old style:

```php
/**
 * @Filter({"name": "StringTrim", "options": {"charlist": " "}, "priority": -100})
 */
```

New style:

```php
/**
 * @Filter("StringStrim", options={"charlist": " "}, priority=-100)
 */
```

#### Hydrator

Passing a single array to the constructor of [`Laminas\Form\Annotation\Hydrator`](../form-creation/attributes-or-annotations.md#hydrator) is deprecated since 3.0.0, please use separate parameters.

Old style:

```php
/**
 * @Hydrator({"type": "Laminas\Hydrator\ClassMethodsHydrator", "options": {"underscoreSeparatedKeys": false}})
 */
```

New style:

```php
/**
 * @Hydrator("Laminas\Hydrator\ClassMethodsHydrator", options={"underscoreSeparatedKeys": false})
 */
```

#### Validator

Passing a single array to the constructor of [`Laminas\Form\Annotation\Validator`](../form-creation/attributes-or-annotations.md#validator) is deprecated since 3.0.0, please use separate parameters.

Old style:

```php
/**
 * @Validator({"name": "StringLength", "options": {"min":3,"max":25}, "break_chain_on_failure": true, "priority": -100})
 */
```

New style:

```php
/**
 * @Validator("StringLength", options={"min": 3, "max": 25}, breakChainOnFailure=true, priority=-100)
 */
```
