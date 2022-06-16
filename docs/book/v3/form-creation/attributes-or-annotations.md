# Using PHP8 Attributes or DocBlock Annotations

Creating a complete form solution can often be tedious: you'll create a domain
model object, an input filter for validating it, a form object for providing a
representation for it, and potentially a hydrator for mapping the form elements
and fieldsets to the domain model. Wouldn't it be nice to have a central place
to define all of these?

Annotations allow us to solve this problem. With v3, laminas-form supports two
different types of annotations: PHP8 attributes and traditional DocBlock annotations.
For new projects, PHP8 attributes are recommended.

## Using PHP8 Attributes

General information on PHP attributes are available in the
[PHP documentation](https://www.php.net/manual/en/language.attributes.overview.php).
Besides the obvious requirement of PHP 8.0 or newer, there are no further dependencies
for using PHP attributes for form creation.

To use attributes, add them to your class and/or properties. The attribute names
will be resolved according to the import statements in your class; as such, you can
make them as long or as short as you want depending on what you import.

Here is an example:

```php
use Laminas\Filter\StringTrim;
use Laminas\Form\Annotation;
use Laminas\Form\Element\Email;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Validator\Regex;
use Laminas\Validator\StringLength;

#[Annotation\Name("user")]
#[Annotation\Hydrator(ObjectPropertyHydrator::class)]
class User
{
    #[Annotation\Exclude]
    public $id;

    #[Annotation\Filter(StringTrim::class)]
    #[Annotation\Validator(StringLength::class, options: ["min" => 1, "max" => 25])]
    #[Annotation\Validator(Regex::class, options: ["pattern" => "/^[a-zA-Z][a-zA-Z0-9_-]{0,24}$/"])]
    #[Annotation\Attributes(["autofocus" => true])]
    #[Annotation\Options(["label" => "Username:"])]
    public $username;

    #[Annotation\Type(Email::class)]
    #[Annotation\Options(["label" => "Your email address:"])]
    public $email;
}
```

The above will hint to the attribute builder to create a form with name "user",
which uses the hydrator `Laminas\Hydrator\ObjectPropertyHydrator`. That form will
have two elements, "username" and "email". The "username" element will have an
associated input that has a `StringTrim` filter, and two validators: a
`StringLength` validator indicating the username is between 1 and 25 characters,
and a `Regex` validator asserting it follows a specific accepted pattern. The
form element itself will have an attribute "autofocus", and a label "Username:".
The "email" element will be of type `Laminas\Form\Element\Email`, and have the label
"Your email address:".

To use the above, we need an object of `Laminas\Form\Annotation\AttributeBuilder`:

```php
use Laminas\Form\Annotation\AttributeBuilder;

$builder = new AttributeBuilder();
$form    = $builder->createForm(User::class);
```

## Using DocBlock Annotations

Besides PHP8-only attributes, laminas-form continues to support traditional
DocBlock annotations with v3, as they have been supported with v2 already. Using
them is suitable for legacy projects or if you need support for PHP 7.

To use DocBlock annotations, include them in your class and/or property docblocks.
Annotation names will be resolved according to the import statements in your
class; as such, you can make them as long or as short as you want depending on
what you import.

> ### Installation Requirements
>
> DocBlock annotations require
> [Doctrine's annotation parser `doctrine\annotations`](https://www.doctrine-project.org/projects/annotations.html)
> as a peer dependency, which contains an annotation parsing engine. You need to
> manually install it using Composer:
>
> ```bash
> $ composer require doctrine/annotations
> ```

Here is the same example from above using DocBlock annotations:

```php
use Laminas\Form\Annotation;

/**
 * @Annotation\Name("user")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 */
class User
{
    /**
     * @Annotation\Exclude()
     */
    public $id;

    /**
     * @Annotation\Filter("StringTrim")
     * @Annotation\Validator("StringLength", options={"min":1, "max":25})
     * @Annotation\Validator("Regex", options={"pattern":"/^[a-zA-Z][a-zA-Z0-9_-]{0,24}$/"})
     * @Annotation\Attributes({"autofocus":true})
     * @Annotation\Options({"label":"Username:"})
     */
    public $username;

    /**
     * @Annotation\Type("Email")
     * @Annotation\Options({"label":"Your email address:"})
     */
    public $email;
}
```

To create a form based on the above annotations, in contrast to the
`AttributeBuilder` from the previous section, we now need to use
`Laminas\Form\Annotation\AnnotationBuilder`. The usage, however, is the same:

```php
use Laminas\Form\Annotation\AnnotationBuilder;

$builder = new AnnotationBuilder();
$form    = $builder->createForm(User::class);
```

At this point, you have a form with the appropriate hydrator attached, an input
filter with the appropriate inputs, and all elements.

## List of Supported Annotations

### AllowEmpty

> ### Deprecated
>
> This annotation is deprecated, please add a `NotEmpty` validator instead.

Marks an input as allowing an empty value. This annotation does not require a value.

```php
/**
 * @AllowEmpty
 */
protected $myProperty;
```

```php
#[AllowEmpty]
protected $myProperty;
```

### Attributes

Used to specify the form, fieldset, or element attributes. This
annotation requires an associative array of values. For DocBlock annotations,
the array has to be in JSON format.

```php
/**
 * @Attributes({"id":"my_property_element","class":"laminas_form"}
 */
protected $myProperty;
```

```php
#[Attributes(["id" => "my_property_element", "class" => "laminas_form"])]
protected $myProperty;
```

### ContinueIfEmpty

> ### Deprecated
>
> This annotation is deprecated, please add a `NotEmpty` validator instead.

Indicate whether the element can be submitted when it is empty. A boolean
value is expected, defaulting to `true`. If `@Required` is set to `false`,
`@ContinueIfEmpty(true)` or simply `@ContinueIfEmpty()`needs to be specified
to allow the field to be empty.

```php
/**
 * @ContinueIfEmpty()
 */
protected $myProperty;
```

```php
#[ContinueIfEmpty()]
protected $myProperty;
```

### ComposedObject

Specify another object with annotations to parse. Typically, this is used if a
property references another object, which will then be added to your form as an
additional fieldset. Expects a string value indicating the class for the object
being composed. To compose a collection, the `isCollection` parameter needs to be
set to `true` and additional `options` can be passed into the collection. `options`
must be an associative array, for DocBlock annotations encoded as JSON.

```php
/**
 * @ComposedObject("Namespace\Model\ComposedObject")
 */
protected $myProperty;

/**
 * @ComposedObject("Namespace\Model\ComposedObject", isCollection=true, options={"count":2})
 */
protected $myOtherProperty;
```

```php
#[ComposedObject(\Namespace\Model\ComposedObject::class)]
protected $myProperty;

#[ComposedObject(\Namespace\Model\ComposedObject::class, isCollection: true, options: ["count" => 2])]
protected $myOtherProperty;
```

### ErrorMessage

Specify the error message to return for an element in the case of a failed
validation. Expects a string value.

```php
/**
 * @ErrorMessage("This is a customized error message.")
 */
protected $myProperty;
```

```php
#[ErrorMessage("This is a customized error message.")]
protected $myProperty;
```

### Filter

Used to provide a specification for a filter to use on a given element.
Expects a name pointing to a string filter name or class, and
optionally further `options` to pass to the constructor of the filter.
`options` must be an associative array, for DocBlock annotations encoded
as JSON.

Additionally, you can use the `priority` argument to modify the order
of the filters in the [filter chain](https://docs.laminas.dev/laminas-filter/filter-chains/).

This annotation may be specified multiple times.

```php
/**
 * @Filter("Boolean", options={"casting":true}, priority=-100)
 */
protected $myProperty;

/**
 * @Filter("Laminas\Filter\Boolean", options={"casting":true})
 */
protected $myOtherProperty;
```

```php
#[Filter("Boolean", options: ["casting" => true]), priority=-100]
protected $myProperty;

#[Filter(\Laminas\Filter\Boolean::class, options: ["casting" => true])]
protected $myOtherProperty;
```

Through the `FilterPluginManager` of laminas-filter, both short names
(like `Boolean`) and fully-qualified class names are supported.

### Flags

Additional flags to pass to the fieldset or form composing an element or
fieldset; these are usually used to specify the name or priority. The
annotation expects an associative array, for DocBlock annotations encoded
as JSON.

```php
/**
 * @Flags({"priority": 100})
 */
protected $myProperty;
```

```php
#[Flags(["priority" => 100])]
protected $myProperty;
```

### Hydrator

Specify the hydrator class to use for this given form or fieldset.
A string value is expected.

```php
/**
 * @Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 */
protected $myProperty;
```

```php
#[Hydrator(\Laminas\Hydrator\ObjectPropertyHydrator::class)]
protected $myProperty;
```

### InputFilter

Specify the input filter class to use for this given form or fieldset. A string
value is expected.

```php
/**
 * @InputFilter("Laminas\InputFilter\InputFilter")
 */
protected $myProperty;
```

```php
#[InputFilter(\Laminas\InputFilter\InputFilter::class)]
protected $myProperty;
```

### Input

Specify the input class to use for this given element. A string value is expected.

```php
/**
 * @Input("Laminas\InputFilter\Input")
 */
protected $myProperty;
```

```php
#[Input(\Laminas\InputFilter\Input::class)]
protected $myProperty;
```

### Instance

Specify an object class instance to bind to the form or fieldset.

```php
/**
 * @Instance("Namespace\Model\Entity")
 */
protected $myProperty;
```

```php
#[Instance(\Namespace\Model\Entity::class)]
protected $myProperty;
```

### Name

Specify the name of the current element, fieldset, or form. A string value is expected.

```php
/**
 * @Name("my_property")
 */
protected $myProperty;
```

```php
#[Name("my_property"]
protected $myProperty;
```

### Options

Options to pass to the fieldset or form that are used to inform behavior &mdash;
things that are not attributes; e.g. labels, CAPTCHA adapters, etc. The annotation
expects an associative array, which for DocBlock annotations needs to be JSON encoded.

```php
/**
 * @Options({"label": "Username:"})
 */
protected $myProperty;
```

```php
#[Options(["label" => "Username:"])]
protected $myProperty;
```

### Required

This annotations indicates whether an element is required. A boolean value is
expected. By default, all elements are required, so this annotation is mainly
present to allow disabling a requirement.

```php
/**
 * @Required(false)
 */
protected $myProperty;
```

```php
#[Required(false)]
protected $myProperty;
```

### Type

Indicates the class to use for the current element, fieldset, or form. A string
value is expected. See the [list of available elements](../element/intro.md).

```php
/**
 * @Type("Email")
 */
protected $myProperty;
```

```php
#[Type("Email")]
protected $myProperty;

#[Type(\Laminas\Form\Element\Email::class)]
protected $myOtherProperty;
```

Through the `FormElementManager` of laminas-form, both short names
(like `Email`) and fully-qualified class names are supported.

### Validator

Used to provide a specification for a validator to use on a given element.
Expects a name pointing to a string validator name or class, and
optionally further `options` to pass to the constructor of the validator.
`options` must be an associative array, for DocBlock annotations encoded
as JSON.

Additionally, you can use the `breakChainOnFailure` and the `priority`
argument to modify the [validator chain](https://docs.laminas.dev/laminas-validator/validator-chains/).

This annotation may be specified multiple times.

```php
/**
 * @Validator("StringLength", options={"min":3, "max":25}, breakChainOnFailure=true)
 * @Validator("Laminas\Validator\Regex", options={"pattern": "/^[a-zA-Z]/"})
 */
protected $myProperty;
```

```php
#[Validator("StringLength", options: ["min" => 3, "max" => 25]), breakChainOnFailure: true]
#[Validator(\Laminas\Validator\Regex::class, options: ["pattern" => "/^[a-zA-Z]/"])]
protected $myProperty;
```

Through the `ValidatorPluginManager` of laminas-validator, both short names
(like `StringLength`) and fully-qualified class names are supported.
