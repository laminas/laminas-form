# Form Elements

## Introduction

A set of specialized elements are provided for accomplishing application-centric tasks. These
include several HTML5 input elements with matching server-side validators, the `Csrf` element (to
prevent Cross Site Request Forgery attacks), and the `Captcha` element (to display and validate
\[CAPTCHAs\](zend.captcha)).

A `Factory` is provided to facilitate creation of elements, fieldsets, forms, and the related input
filter. See the \[Zend\\Form Quick Start\](zend.form.quick-start.factory) for more information.

orphan  

## Element Base Class

`Zend\Form\Element` is a base class for all specialized elements and `Zend\Form\Fieldset`.

**Basic Usage**

At the bare minimum, each element or fieldset requires a name. You will also typically provide some
attributes to hint to the view layer how it might render the item.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$username = new Element\Text('username');
$username
    ->setLabel('Username')
    ->setAttributes(array(
        'class' => 'username',
        'size'  => '30',
    ));

$password = new Element\Password('password');
$password
    ->setLabel('Password')
    ->setAttributes(array(
        'size'  => '30',
    ));

$form = new Form('my-form');
$form
    ->add($username)
    ->add($password);
```

**Public Methods**

## Standard Elements

orphan  

### Button

`Zend\Form\Element\Button` represents a button form input. It can be used with the
`Zend\Form\View\Helper\FormButton` view helper.

`Zend\Form\Element\Button` extends from \[ZendFormElement\](zend.form.element).

**Basic Usage**

This element automatically adds a `type` attribute of value `button`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$button = new Element\Button('my-button');
$button->setLabel('My Button')
       ->setValue('foo');

$form = new Form('my-form');
$form->add($button);
```

orphan  

### Captcha

`Zend\Form\Element\Captcha` can be used with forms where authenticated users are not necessary, but
you want to prevent spam submissions. It is paired with one of the `Zend\Form\View\Helper\Captcha\*`
view helpers that matches the type of *CAPTCHA* adapter in use.

**Basic Usage**

A *CAPTCHA* adapter must be attached in order for validation to be included in the element's input
filter specification. See the section on \[Zend CAPTCHA Adapters\](zend.captcha.adapters) for more
information on what adapters are available.

```php
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;

$captcha = new Element\Captcha('captcha');
$captcha
    ->setCaptcha(new Captcha\Dumb())
    ->setLabel('Please verify you are human');

$form = new Form('my-form');
$form->add($captcha);
```

Here is with the array notation:

```php
use Zend\Captcha;
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Captcha',
    'name' => 'captcha',
    'options' => array(
        'label' => 'Please verify you are human',
        'captcha' => new Captcha\Dumb(),
    ),
));
```

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element
&lt;zend.form.element.methods&gt;.

orphan  

### Checkbox

`Zend\Form\Element\Checkbox` is meant to be paired with the `Zend\Form\View\Helper\FormCheckbox` for
HTML inputs with type checkbox. This element adds an `InArray` validator to its input filter
specification in order to validate on the server if the checkbox contains either the checked value
or the unchecked value.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"checkbox"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$checkbox = new Element\Checkbox('checkbox');
$checkbox->setLabel('A checkbox');
$checkbox->setUseHiddenElement(true);
$checkbox->setCheckedValue("good");
$checkbox->setUncheckedValue("bad");

$form = new Form('my-form');
$form->add($checkbox);
```

Using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');       
$form->add(array(
    'type' => 'Zend\Form\Element\Checkbox',
    'name' => 'checkbox',
    'options' => array(
        'label' => 'A checkbox',
        'use_hidden_element' => true,
        'checked_value' => 'good',
        'unchecked_value' => 'bad'
    )
));
```

When creating a checkbox element, setting an attribute of checked will result in the checkbox always
being checked regardless of any data object which might subsequently be bound to the form. The
correct way to set the default value of a checkbox is to set the value attribute as for any other
element. To have a checkbox checked by default make the value equal to the checked\_value eg:

```php
use Zend\Form\Form;

$form = new Form('my-form');       
$form->add(array(
    'type' => 'Zend\Form\Element\Checkbox',
    'name' => 'checkbox',
    'options' => array(
        'label' => 'A checkbox',
        'use_hidden_element' => true,
        'checked_value' => 'yes',
        'unchecked_value' => 'no'
    ),
    'attributes' => array(
         'value' => 'yes'
    )
));
```

**Public Methods**

The following methods are in addition to the inherited \[methods of
Zend\\Form\\Element\](zend.form.element.methods) .

orphan  

### Collection

Sometimes, you may want to add input (or a set of inputs) multiple times, either because you don't
want to duplicate code, or because you do not know in advance how many elements you will need (in
the case of elements dynamically added to a form using JavaScript, for instance). For more
information about Collection, please refer to the \[Form Collections
tutorial\](zend.form.collections).

`Zend\Form\Element\Collection` is meant to be paired with the
`Zend\Form\View\Helper\FormCollection`.

**Basic Usage**

```php
use Zend\Form\Element;
use Zend\Form\Form;

$colors = new Element\Collection('collection');
$colors->setLabel('Colors');
$colors->setCount(2);
$colors->setTargetElement(new Element\Color());
$colors->setShouldCreateTemplate(true);

$form = new Form('my-form');
$form->add($colors);
```

Using the array notation:

```php
use Zend\Form\Element;
use Zend\Form\Form;

$form = new Form('my-form');       
$form->add(array(
    'type' => 'Zend\Form\Element\Collection',
    'options' => array(
        'label' => 'Colors',
        'count' => 2,
        'should_create_template' => true,
        'target_element' => new Element\Color()
    )
));
```

**Public Methods**

The following methods are in addition to the inherited \[methods of
Zend\\Form\\Element\](zend.form.element.methods) .

orphan  

### Csrf

`Zend\Form\Element\Csrf` pairs with the `Zend\Form\View\Helper\FormHidden` to provide protection
from *CSRF* attacks on forms, ensuring the data is submitted by the user session that generated the
form and not by a rogue script. Protection is achieved by adding a hash element to a form and
verifying it when the form is submitted.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"hidden"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$csrf = new Element\Csrf('csrf');

$form = new Form('my-form');
$form->add($csrf);
```

You can change the options of the CSRF validator using the `setCsrfValidatorOptions` function, or by
using the `"csrf_options"` key. Here is an example using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Csrf',
    'name' => 'csrf',
    'options' => array(
        'csrf_options' => array(
            'timeout' => 600
        )
    )
));
```

> ## Note
If you are using more than one form on a page, and each contains its own CSRF element, you will need
to make sure that each form uniquely names its element; if you do not, it's possible for the value
of one to override the other within the server-side session storage, leading to the inability to
validate one or more of the forms on your page. We suggest prefixing the element name with the
form's name or function: "login\_csrf", "registration\_csrf", etc.

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element
&lt;zend.form.element.methods&gt;.

orphan  

### File

`Zend\Form\Element\File` represents a form file input and provides a default input specification
with a type of \[FileInput\](zend.input-filter.file-input) (important for handling validators and
filters correctly). It can be used with the `Zend\Form\View\Helper\FormFile` view helper.

`Zend\Form\Element\File` extends from \[Zend\\Form\\Element\](zend.form.element).

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"file"`. It will also set the form's
enctype to `multipart/form-data` during `$form->prepare()`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

// Single file upload
$file = new Element\File('file');
$file->setLabel('Single file input');

// HTML5 multiple file upload
$multiFile = new Element\File('multi-file');
$multiFile->setLabel('Multi file input')
          ->setAttribute('multiple', true);

$form = new Form('my-file');
$form->add($file)
     ->add($multiFile);
```

orphan  

### Hidden

`Zend\Form\Element\Hidden` represents a hidden form input. It can be used with the
`Zend\Form\View\Helper\FormHidden` view helper.

`Zend\Form\Element\Hidden` extends from \[Zend\\Form\\Element\](zend.form.element).

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"hidden"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$hidden = new Element\Hidden('my-hidden');
$hidden->setValue('foo');

$form = new Form('my-form');
$form->add($hidden);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Hidden',
    'name' => 'my-hidden',
    'attributes' => array(
        'value' => 'foo'
    )
));
```

orphan  

### Image

`Zend\Form\Element\Image` represents a image button form input. It can be used with the
`Zend\Form\View\Helper\FormImage` view helper.

`Zend\Form\Element\Image` extends from \[Zend\\Form\\Element\](zend.form.element).

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"image"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$image = new Element\Image('my-image');
$image->setAttribute('src', 'http://my.image.url'); // Src attribute is required

$form = new Form('my-form');
$form->add($image);
```

orphan  

### Month Select

`Zend\Form\Element\MonthSelect` is meant to be paired with the
`Zend\Form\View\Helper\FormMonthSelect`. This element creates two select elements, where the first
one is populated with months and the second is populated with years. By default, it sets 100 years
in the past for the year element, starting with the current year.

**Basic Usage**

```php
use Zend\Form\Element;
use Zend\Form\Form;

$monthYear = new Element\MonthSelect('monthyear');
$monthYear->setLabel('Select a month and a year');
$monthYear->setMinYear(1986);

$form = new Form('dateselect');
$form->add($monthYear);
```

Using the array notation:

```php
use Zend\Form\Form;

$form = new Form('dateselect');
$form->add(array(
    'type' => 'Zend\Form\Element\MonthSelect',
    'name' => 'monthyear',
    'options' => array(
        'label' => 'Select a month and a year',
        'min_year' => 1986,
    )
));
```

**Public Methods**

The following methods are in addition to the inherited \[methods of
Zend\\Form\\Element\](zend.form.element.methods).

orphan  

### MultiCheckbox

`Zend\Form\Element\MultiCheckbox` is meant to be paired with the
`Zend\Form\View\Helper\FormMultiCheckbox` for HTML inputs with type checkbox. This element adds an
`InArray` validator to its input filter specification in order to validate on the server if the
checkbox contains values from the multiple checkboxes.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"checkbox"` for every checkboxes.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$multiCheckbox = new Element\MultiCheckbox('multi-checkbox');
$multiCheckbox->setLabel('What do you like ?');
$multiCheckbox->setValueOptions(array(
        '0' => 'Apple',
        '1' => 'Orange',
        '2' => 'Lemon'
));

$form = new Form('my-form');
$form->add($multiCheckbox);
```

Using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\MultiCheckbox',
    'name' => 'multi-checkbox',
    'options' => array(
        'label' => 'What do you like ?',
        'value_options' => array(
            '0' => 'Apple',
            '1' => 'Orange',
            '2' => 'Lemon',
        ),
    )
));
```

**Advanced Usage**

In order to set attributes or customize the option elements, an array can be used instead of a
string. The following keys are supported:

- `"label"` - The string displayed for the option.
- `"value"` - The form value associated with the option.
- `"selected"` - Boolean that sets whether the option is marked as selected.
- `"disabled"` - Boolean that sets whether the option will be disabled
- `"attributes"` - Array of html attributes that will be set on this option. Merged with the
attributes set on the element.
- `"label_attributes"` - Array of html attributes that will be set on the label. Merged with the
attributes set on the element's label.

```php
$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\MultiCheckbox',
    'name' => 'multi-checkbox',
    'options' => array(
        'label' => 'What do you like ?',
        'value_options' => array(
            array(
                'value' => '0',
                'label' => 'Apple',
                'selected' => false,
                'disabled' => false,
                'attributes' => array(
                    'id' => 'apple_option',
                    'data-fruit' => 'apple',
                ),
                'label_attributes' => array(
                    'id' => 'apple_label',
                ),
            ),
            array(
                'value' => '1',
                'label' => 'Orange',
                'selected' => true,
            ),
            array(
                'value' => '2',
                'label' => 'Lemon',
            ),
        ),
    ),
));
```

**Public Methods**

The following methods are in addition to the inherited \[methods of
Zend\\Form\\Element\\Checkbox\](zend.form.element.checkbox.methods) .

orphan  

### Password

`Zend\Form\Element\Password` represents a password form input. It can be used with the
`Zend\Form\View\Helper\FormPassword` view helper.

`Zend\Form\Element\Password` extends from \[Zend\\Form\\Element\](zend.form.element).

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"password"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$password = new Element\Password('my-password');
$password->setLabel('Enter your password');

$form = new Form('my-form');
$form->add($password);
```

orphan  

### Radio

`Zend\Form\Element\Radio` is meant to be paired with the `Zend\Form\View\Helper\FormRadio` for HTML
inputs with type radio. This element adds an `InArray` validator to its input filter specification
in order to validate on the server if the value is contains within the radio value elements.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"radio"` for every radio.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$radio = new Element\Radio('gender');
$radio->setLabel('What is your gender ?');
$radio->setValueOptions(array(
    '0' => 'Female',
    '1' => 'Male',
));

$form = new Form('my-form');
$form->add($radio);
```

Using the array notation:

```php
use Zend\Form\Form;

   $form = new Form('my-form');
   $form->add(array(
       'type' => 'Zend\Form\Element\Radio',
       'name' => 'gender',
       'options' => array(
           'label' => 'What is your gender ?',
           'value_options' => array(
               '0' => 'Female',
               '1' => 'Male',
           ),
       ),
   ));
```

**Advanced Usage**

See MultiCheckbox for examples&lt;zend.form.element.multicheckbox.advanced&gt; of how to apply
attributes and options to each radio button.

**Public Methods**

All the methods from the inherited \[methods of
Zend\\Form\\Element\\MultiCheckbox\](zend.form.element.multicheckbox.methods) are also available for
this element.

orphan  

### Select

`Zend\Form\Element\Select` is meant to be paired with the `Zend\Form\View\Helper\FormSelect` for
HTML inputs with type select. This element adds an `InArray` validator to its input filter
specification in order to validate on the server if the selected value belongs to the values. This
element can be used as a multi-select element by adding the "multiple" HTML attribute to the
element.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"select"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$select = new Element\Select('language');
$select->setLabel('Which is your mother tongue?');
$select->setValueOptions(array(
    '0' => 'French',
    '1' => 'English',
    '2' => 'Japanese',
    '3' => 'Chinese',
));

$form = new Form('language');
$form->add($select);
```

Using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Select',
    'name' => 'language',
    'options' => array(
        'label' => 'Which is your mother tongue?',
        'value_options' => array(
            '0' => 'French',
            '1' => 'English',
            '2' => 'Japanese',
            '3' => 'Chinese',
        ),
    )
));
```

You can add an empty option (option with no value) using the `"empty_option"` option:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Select',
    'name' => 'language',
    'options' => array(
        'label' => 'Which is your mother tongue?',
        'empty_option' => 'Please choose your language',
        'value_options' => array(
            '0' => 'French',
            '1' => 'English',
            '2' => 'Japanese',
            '3' => 'Chinese',
        ),
    )
));
```

Option groups are also supported. You just need to add an 'options' key to the value options.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$select = new Element\Select('language');
$select->setLabel('Which is your mother tongue?');
$select->setValueOptions(array(
     'european' => array(
        'label' => 'European languages',
        'options' => array(
           '0' => 'French',
           '1' => 'Italian',
        ),
     ),
     'asian' => array(
        'label' => 'Asian languages',
        'options' => array(
           '2' => 'Japanese',
           '3' => 'Chinese',
        ),
     ),
));

$form = new Form('language');
$form->add($select);
```

**Public Methods**

The following methods are in addition to the inherited \[methods of
Zend\\Form\\Element\](zend.form.element.methods) .

orphan  

### Submit

`Zend\Form\Element\Submit` represents a submit button form input. It can be used with the
`Zend\Form\View\Helper\FormSubmit` view helper.

`Zend\Form\Element\Submit` extends from \[Zend\\Form\\Element\](zend.form.element).

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"submit"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$submit = new Element\Submit('my-submit');
$submit->setValue('Submit Form');

$form = new Form('my-form');
$form->add($submit);
```

orphan  

### Text

`Zend\Form\Element\Text` represents a text form input. It can be used with the
`Zend\Form\View\Helper\FormText` view helper.

`Zend\Form\Element\Text` extends from \[Zend\\Form\\Element\](zend.form.element).

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"text"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$text = new Element\Text('my-text');
$text->setLabel('Enter your name');

$form = new Form('my-form');
$form->add($text);
```

orphan  

### Textarea

`Zend\Form\Element\Textarea` represents a textarea form input. It can be used with the
`Zend\Form\View\Helper\FormTextarea` view helper.

`Zend\Form\Element\Textarea` extends from \[Zend\\Form\\Element\](zend.form.element).

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"textarea"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$textarea = new Element\Textarea('my-textarea');
$textarea->setLabel('Enter a description');

$form = new Form('my-form');
$form->add($textarea);
```

## HTML5 Elements

orphan  

### Color

`Zend\Form\Element\Color` is meant to be paired with the `Zend\Form\View\Helper\FormColor` for
[HTML5 inputs with type
color](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#color-state-(type=color)).
This element adds filters and a `Regex` validator to it's input filter specification in order to
validate a [HTML5 valid simple
color](http://www.whatwg.org/specs/web-apps/current-work/multipage/common-microsyntaxes.html#valid-simple-color)
value on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"color"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$color = new Element\Color('color');
$color->setLabel('Background color');

$form = new Form('my-form');
$form->add($color);
```

Here is the same example using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Color',
    'name' => 'color',
    'options' => array(
        'label' => 'Background color'
    )
));
```

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element
&lt;zend.form.element.methods&gt;.

orphan  

### Date

`Zend\Form\Element\Date` is meant to be paired with the `Zend\Form\View\Helper\FormDate` for [HTML5
inputs with type
date](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#date-state-(type=date)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 date input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"date"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$date = new Element\Date('appointment-date');
$date
    ->setLabel('Appointment Date')
    ->setAttributes(array(
        'min'  => '2012-01-01',
        'max'  => '2020-01-01',
        'step' => '1', // days; default step interval is 1 day
    ))
    ->setOptions(array(
        'format' => 'Y-m-d'
    ));

$form = new Form('my-form');
$form->add($date);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Date',
    'name' => 'appointment-date',
    'options' => array(
        'label' => 'Appointment Date',
        'format' => 'Y-m-d'
    ),
    'attributes' => array(
        'min' => '2012-01-01',
        'max' => '2020-01-01',
        'step' => '1', // days; default step interval is 1 day
    )
));
```

> ## Note
Note: the `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

**Public Methods**

The following methods are in addition to the inherited methods of
Zend\\\\Form\\\\Element\\\\DateTime
&lt;zend.form.element.date-time.methods&gt;.

orphan  

### DateTime

`Zend\Form\Element\DateTime` is meant to be paired with the `Zend\Form\View\Helper\FormDateTime` for
[HTML5 inputs with type
datetime](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#date-and-time-state-(type=datetime)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 datetime input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"datetime"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$dateTime = new Element\DateTime('appointment-date-time');
$dateTime
    ->setLabel('Appointment Date/Time')
    ->setAttributes(array(
        'min'  => '2010-01-01T00:00:00Z',
        'max'  => '2020-01-01T00:00:00Z',
        'step' => '1', // minutes; default step interval is 1 min
    ))
    ->setOptions(array(
        'format' => 'Y-m-d\TH:iP'
    ));

$form = new Form('my-form');
$form->add($dateTime);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\DateTime',
    'name' => 'appointment-date-time',
    'options' => array(
        'label' => 'Appointment Date/Time',
        'format' => 'Y-m-d\TH:iP'
    ),
    'attributes' => array(
        'min' => '2010-01-01T00:00:00Z',
        'max' => '2020-01-01T00:00:00Z',
        'step' => '1', // minutes; default step interval is 1 min
    )
));
```

> ## Note
Note: the `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element
&lt;zend.form.element.methods&gt;.

orphan  

### DateTimeLocal

`Zend\Form\Element\DateTimeLocal` is meant to be paired with the
`Zend\Form\View\Helper\FormDateTimeLocal` for [HTML5 inputs with type
datetime-local](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#local-date-and-time-state-(type=datetime-local)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 a local datetime input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"datetime-local"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$dateTimeLocal = new Element\DateTimeLocal('appointment-date-time');
$dateTimeLocal
    ->setLabel('Appointment Date')
    ->setAttributes(array(
        'min'  => '2010-01-01T00:00:00',
        'max'  => '2020-01-01T00:00:00',
        'step' => '1', // minutes; default step interval is 1 min
    ))
    ->setOptions(array(
        'format' => 'Y-m-d\TH:i'
    ));

$form = new Form('my-form');
$form->add($dateTimeLocal);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\DateTimeLocal',
    'name' => 'appointment-date-time',
    'options' => array(
        'label'  => 'Appointment Date',
        'format' => 'Y-m-d\TH:i'
    ),
    'attributes' => array(
        'min' => '2010-01-01T00:00:00',
        'max' => '2020-01-01T00:00:00',
        'step' => '1', // minutes; default step interval is 1 min
    )
));
```

> ## Note
Note: the `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

**Public Methods**

The following methods are in addition to the inherited methods of
Zend\\\\Form\\\\Element\\\\DateTime
&lt;zend.form.element.date-time.methods&gt;.

orphan  

### Email

`Zend\Form\Element\Email` is meant to be paired with the `Zend\Form\View\Helper\FormEmail` for
[HTML5 inputs with type
email](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-(type=email)).
This element adds filters and validators to it's input filter specification in order to validate
[HTML5 valid email
address](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#valid-e-mail-address)
on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"email"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$form = new Form('my-form');

// Single email address
$email = new Element\Email('email');
$email->setLabel('Email Address')
$form->add($email);

// Comma separated list of emails
$emails = new Element\Email('emails');
$emails
    ->setLabel('Email Addresses')
    ->setAttribute('multiple', true);
$form->add($emails);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Email',
    'name' => 'email',
    'options' => array(
        'label' => 'Email Address'
    ),
));

$form->add(array(
    'type' => 'Zend\Form\Element\Email',
    'name' => 'emails',
    'options' => array(
        'label' => 'Email Addresses'
    ),
    'attributes' => array(
        'multiple' => true
    )
));
```

> ## Note
Note: the `multiple` attribute should be set prior to calling Zend\\Form::prepare(). Otherwise, the
default input specification for the element may not contain the correct validation rules.

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element
&lt;zend.form.element.methods&gt;.

orphan  

### Month

`Zend\Form\Element\Month` is meant to be paired with the `Zend\Form\View\Helper\FormMonth` for
[HTML5 inputs with type
month](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#month-state-(type=month)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 month input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"month"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$month = new Element\Month('month');
$month
    ->setLabel('Month')
    ->setAttributes(array(
        'min'  => '2012-01',
        'max'  => '2020-01',
        'step' => '1', // months; default step interval is 1 month
    ));

$form = new Form('my-form');
$form->add($month);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Month',
    'name' => 'month',
    'options' => array(
        'label' => 'Month'
    ),
    'attributes' => array(
        'min' => '2012-12',
        'max' => '2020-01',
        'step' => '1', // months; default step interval is 1 month
    )
));
```

> ## Note
Note: the `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

**Public Methods**

The following methods are in addition to the inherited methods of
Zend\\\\Form\\\\Element\\\\DateTime
&lt;zend.form.element.date-time.methods&gt;.

orphan  

### Number

`Zend\Form\Element\Number` is meant to be paired with the `Zend\Form\View\Helper\FormNumber` for
[HTML5 inputs with type
number](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#number-state-(type=number)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 number input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"number"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$number = new Element\Number('quantity');
$number
    ->setLabel('Quantity')
    ->setAttributes(array(
        'min'  => '0',
        'max'  => '10',
        'step' => '1', // default step interval is 1
    ));

$form = new Form('my-form');
$form->add($number);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Number',
    'name' => 'quantity',
    'options' => array(
        'label' => 'Quantity'
    ),
    'attributes' => array(
        'min' => '0',
        'max' => '10',
        'step' => '1', // default step interval is 1
    )
));
```

> ## Note
Note: the `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element
&lt;zend.form.element.methods&gt;.

orphan  

### Range

`Zend\Form\Element\Range` is meant to be paired with the `Zend\Form\View\Helper\FormRange` for
[HTML5 inputs with type
range](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#range-state-(type=range)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 range values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"range"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$range = new Element\Range('range');
$range
    ->setLabel('Minimum and Maximum Amount')
    ->setAttributes(array(
        'min'  => '0',   // default minimum is 0
        'max'  => '100', // default maximum is 100
        'step' => '1',   // default interval is 1
    ));

$form = new Form('my-form');
$form->add($range);
```

Here is with the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Range',
    'name' => 'range',
    'options' => array(
        'label' => 'Minimum and Maximum Amount'
    ),
    'attributes' => array(
        'min' => 0, // default minimum is 0
        'max' => 100, // default maximum is 100
        'step' => 1 // default interval is 1
    )
));
```

> ## Note
Note: the `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element\\\\Number
&lt;zend.form.element.number.methods&gt;.

orphan  

### Time

`Zend\Form\Element\Time` is meant to be paired with the `Zend\Form\View\Helper\FormTime` for [HTML5
inputs with type
time](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#time-state-(type=time)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 time input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"time"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$time = new Element\Time('time');
$time
    ->setLabel('Time')
    ->setAttributes(array(
        'min'  => '00:00:00',
        'max'  => '23:59:59',
        'step' => '60', // seconds; default step interval is 60 seconds
    ))
    ->setOptions(array(
        'format' => 'H:i:s'
    ));

$form = new Form('my-form');
$form->add($time);
```

Here is the same example using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Time',
    'name' => 'time',
    'options'=> array(
        'label'  => 'Time',
        'format' => 'H:i:s'
    ),
    'attributes' => array(
        'min' => '00:00:00',
        'max' => '23:59:59',
        'step' => '60', // seconds; default step interval is 60 seconds
    )
));
```

> ## Note
The `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

> ## Note
The default date format for the validator is `H:i:s`. A valid time string is however not required to
have a seconds part. In fact some user agent UIs such as Google Chrome and Opera submits a value on
the `H:i` format (i.e. without a second part). You might therefore want to set the date format
accordingly.

**Public Methods**

The following methods are in addition to the inherited methods of
Zend\\\\Form\\\\Element\\\\DateTime
&lt;zend.form.element.date-time.methods&gt;.

orphan  

### Url

`Zend\Form\Element\Url` is meant to be paired with the `Zend\Form\View\Helper\FormUrl` for [HTML5
inputs with type
url](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#url-state-(type=url)).
This element adds filters and a `Zend\Validator\Uri` validator to it's input filter specification
for validating HTML5 URL input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"url"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$url = new Element\Url('webpage-url');
$url->setLabel('Webpage URL');

$form = new Form('my-form');
$form->add($url);
```

Here is the same example using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Url',
    'name' => 'webpage-url',
    'options' => array(
        'label' => 'Webpage URL'
    )
));
```

**Public Methods**

The following methods are in addition to the inherited methods of Zend\\\\Form\\\\Element
&lt;zend.form.element.methods&gt;.

orphan  

### Week

`Zend\Form\Element\Week` is meant to be paired with the `Zend\Form\View\Helper\FormWeek` for [HTML5
inputs with type
week](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#week-state-(type=week)).
This element adds filters and validators to it's input filter specification in order to validate
HTML5 week input values on the server.

**Basic Usage**

This element automatically adds a `"type"` attribute of value `"week"`.

```php
use Zend\Form\Element;
use Zend\Form\Form;

$week = new Element\Week('week');
$week
    ->setLabel('Week')
    ->setAttributes(array(
        'min'  => '2012-W01',
        'max'  => '2020-W01',
        'step' => '1', // weeks; default step interval is 1 week
    ));

$form = new Form('my-form');
$form->add($week);
```

Here is the same example using the array notation:

```php
use Zend\Form\Form;

$form = new Form('my-form');
$form->add(array(
    'type' => 'Zend\Form\Element\Week',
    'name' => 'week',
    'options' => array(
        'label' => 'Week'
    ),
    'attributes' => array(
        'min' => '2012-W01',
        'max' => '2020-W01',
        'step' => '1', // weeks; default step interval is 1 week
    )
));
```

> ## Note
Note: the `min`, `max`, and `step` attributes should be set prior to calling Zend\\Form::prepare().
Otherwise, the default input specification for the element may not contain the correct validation
rules.

**Public Methods**

The following methods are in addition to the inherited methods of
Zend\\\\Form\\\\Element\\\\DateTime
&lt;zend.form.element.date-time.methods&gt;.
