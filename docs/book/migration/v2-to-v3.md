# Migrating from v2 to v3

laminas-form v3 makes a number of changes that may affect your application. This 
document details those changes, and provides suggestions on how to update your 
application to work with v3.


## Using doctrine/annotations now instead of laminas/laminas-code

Since laminas-code dropped support for annotation parsing with v4, laminas-form 
switched to doctrine/annotations for annotation parsing. For most users, this will
not have any side effects, however, you must ensure that you install 
doctrine/annotations with at least version 1.12.0:

```bash
compose require doctrine/annotations
```

## Support for PHP8 Attributes

laminas-form v3 supports native PHP8 attributes as an alternative to PHPDoc 
annotations. While for new projects it is recommended to use PHP8 attributes, there
is no need to migrate PHPDoc annotations to PHP8 attributes, as laminas-form
will continue to support PHPDoc annotations.

In case you still want to migrate from PHPDoc annotations to PHP8 attributes,
please have a look at the [annotations chapter](../form-creation/using-annotations.md),
which provides a side-by-side comparison of these annotations.

## Single Associative Array in Constructors of Annotations is Deprecated

After upgrading you may receive one or more deprecation notices about passing a 
single array to the constructor of annotation classes. While the old style will
continue to work, it is recommended to update your annotations. Please see the following
list of potential notices and how to eliminate them:

### ComposedObject

> Passing a single array to the constructor of Laminas\Form\Annotation\ComposedObject 
> is deprecated since 3.0.0, please use separate parameters.

Old style:

```
@ComposedObject({"target_object": "Namespace\Model\Entity", "is_collection": "true", "options": {}})
```

New Style:

```
@ComposedObject("Namespace\Model\Entity", isCollection="true", options={})
```

### Filter

> Passing a single array to the constructor of Laminas\Form\Annotation\Filter
> is deprecated since 3.0.0, please use separate parameters.

Old style:

```
@Filter({"name": "StringTrim", "options": {"charlist": " "}, "priority": -100})
```

New Style:

```
@Filter("StringStrim", options={"charlist": " "}, priority=-100)
```

### Hydrator

> Passing a single array to the constructor of Laminas\Form\Annotation\Hydrator
> is deprecated since 3.0.0, please use separate parameters.

Old style:

```
@Hydrator({"type": "Laminas\Hydrator\ClassMethodsHydrator", "options": {"underscoreSeparatedKeys": false}})
```

New Style:

```
@Hydrator("Laminas\Hydrator\ClassMethodsHydrator", options={"underscoreSeparatedKeys": false})
```

### Validator

> Passing a single array to the constructor of Laminas\Form\Annotation\Validator
> is deprecated since 3.0.0, please use separate parameters.

Old style:

```
@Validator({"name": "StringLength", "options": {"min":3,"max":25}, "break_chain_on_failure": true, "priority": -100})
```

New Style:

```
@Validator("StringLength", options={"min": 3, "max": 25}, breakChainOnFailure=true, priority=-100)
```