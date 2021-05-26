# Using Annotations or PHP8 Attributes

Creating a complete form solution can often be tedious: you'll create a domain
model object, an input filter for validating it, a form object for providing a
representation for it, and potentially a hydrator for mapping the form elements
and fieldsets to the domain model. Wouldn't it be nice to have a central place
to define all of these?

Annotations allow us to solve this problem. You can define the following
behaviors with the shipped annotations in laminas-form:

- `AllowEmpty`: mark an input as allowing an empty value. This annotation does
  not require a value.
- `Attributes`: specify the form, fieldset, or element attributes. This
  annotation requires an associative array of values, in a JSON object format:
  `@Attributes({"class":"laminas_form","type":"text"})`.
- `ComposedObject`: specify another object with annotations to parse. Typically,
  this is used if a property references another object, which will then be added
  to your form as an additional fieldset.  Expects a string value indicating the
  class for the object being composed: `@ComposedObject("Namespace\Model\ComposedObject")`;
  or an array to compose a collection:
  `@ComposedObject({ "target_object":"Namespace\Model\ComposedCollection", "is_collection":"true", "options":{"count":2}})`;
  `target_object` is the element to compose, `is_collection` flags this as a
  collection, and `options` can take an array of options to pass into the
  collection.
- `ErrorMessage`: specify the error message to return for an element in the case
  of a failed validation. Expects a string value.
- `Exclude`: mark a property to exclude from the form or fieldset. This
  annotation does not require a value.
- `Filter`: provide a specification for a filter to use on a given element.
  Expects an associative array of values, with a "name" key pointing to a string
  filter name, and an "options" key pointing to an associative array of filter
  options for the constructor: `@Filter({"name": "Boolean", "options": {"casting":true}})`.
  This annotation may be specified multiple times.
- `Flags`: flags to pass to the fieldset or form composing an element or
  fieldset; these are usually used to specify the name or priority. The
  annotation expects an associative array: `@Flags({"priority": 100})`.
- `Hydrator`: specify the hydrator class to use for this given form or fieldset.
  A string value is expected.
- `InputFilter`: specify the input filter class to use for this given form or
  fieldset. A string value is expected.
- `Input`: specify the input class to use for this given element. A string value
  is expected.
- `Instance`: specify an object class instance to bind to the form or fieldset.
- `Name`: specify the name of the current element, fieldset, or form. A string
  value is expected.
- `Options`: options to pass to the fieldset or form that are used to inform
  behavior &mdash; things that are not attributes; e.g. labels, CAPTCHA adapters,
  etc. The annotation expects an associative array: `@Options({"label": "Username:"})`.
- `Required`: indicate whether an element is required. A boolean value is
  expected. By default, all elements are required, so this annotation is mainly
  present to allow disabling a requirement.
- `Type`: indicate the class to use for the current element, fieldset, or form.
  A string value is expected.
- `Validator`: provide a specification for a validator to use on a given
  element. Expects an associative array of values, with a "name" key pointing to
  a string validator name, and an "options" key pointing to an associative array
  of validator options for the constructor:
  `@Validator({"name": "StringLength", "options": {"min":3, "max": 25}})`.
  This annotation may be specified multiple times.
- `ContinueIfEmpty`: indicate whether the element can be submitted when it
  is empty. A boolean value is expected. If `@Required` is set to `false`, this
  needs to be set to `true` to allow the field to be empty.

To use annotations, include them in your class and/or property docblocks.
Annotation names will be resolved according to the import statements in your
class; as such, you can make them as long or as short as you want depending on
what you import.

> ### doctrine/common dependency
>
> Form annotations require `doctrine\common`, which contains an annotation
> parsing engine. Install it using composer:
>
> ```bash
> $ composer require doctrine/common
> ```

Here's an example:

```php
use Laminas\Form\Annotation;

/**
 * @Annotation\Name("user")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 */
class User
{
    /**
     * @Annotation\Exclude()
     */
    public $id;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex",
"options":{"pattern":"/^[a-zA-Z][a-zA-Z0-9_-]{0,24}$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Username:"})
     */
    public $username;

    /**
     * @Annotation\Type("Laminas\Form\Element\Email")
     * @Annotation\Options({"label":"Your email address:"})
     */
    public $email;
}
```

The above will hint to the annotation builder to create a form with name "user",
which uses the hydrator `Laminas\Hydrator\ObjectProperty`. That form will
have two elements, "username" and "email". The "username" element will have an
associated input that has a `StringTrim` filter, and two validators: a
`StringLength` validator indicating the username is between 1 and 25 characters,
and a `Regex` validator asserting it follows a specific accepted pattern. The
form element itself will have an attribute "type" with value "text" (a text
element), and a label "Username:". The "email" element will be of type
`Laminas\Form\Element\Email`, and have the label "Your email address:".

To use the above, we need `Laminas\Form\Annotation\AnnotationBuilder`:

```php
use Laminas\Form\Annotation\AnnotationBuilder;

$builder = new AnnotationBuilder();
$form    = $builder->createForm(User::class);
```

At this point, you have a form with the appropriate hydrator attached, an input
filter with the appropriate inputs, and all elements.
