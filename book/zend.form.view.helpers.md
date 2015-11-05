# Form View Helpers

## Introduction

Zend Framework comes with an initial set of helper classes related to Forms: e.g., rendering a text
input, selection box, or form labels. You can use helper, or plugin, classes to perform these
behaviors for you.

See the section on \[view helpers\](zend.view.helpers) for more information.

## Standard Helpers

orphan  

### Form

The `Form` view helper is used to render a `<form>` HTML element and its attributes.

It iterates through all its elements and relies on the `FormCollection` and `FormRow` view helpers
to render them appropriately.

You can also use \[Zend\\Form\\View\\Helper\\FormRow\](zend.form.view.helper.form-row) in
conjunction with `Form::openTag()` and `Form::closeTag()` to have a more fine grained control over
the output.

Basic usage:

```php
/**
 * inside view template
 *
 * @var \Zend\View\Renderer\PhpRenderer $this
 * @var \Zend\Form\Form $form
 */

echo $this->form($form);
// i.e.
// <form action="" method="POST">
//    <label>
//       <span>Some Label</span>
//       <input type="text" name="some_element" value="">
//    </label>
// </form>
```

The following public methods are in addition to those inherited from
\[Zend\\Form\\View\\Helper\\AbstractHelper\](zend.form.view.helper.abstract-helper.methods).

orphan  

### FormButton

The `FormButton` view helper is used to render a `<button>` HTML element and its attributes.

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Button('my-button');
$element->setLabel("Reset");

// Within your view...

/**
 * Example #1: Render entire button in one shot...
 */
echo $this->formButton($element);
// <button name="my-button" type="button">Reset</button>

/**
 * Example #2: Render button in 3 steps
 */
// Render the opening tag
echo $this->formButton()->openTag($element);
// <button name="my-button" type="button">

echo '<span class="inner">' . $element->getLabel() . '</span>';

// Render the closing tag
echo $this->formButton()->closeTag();
// </button>

/**
 * Example #3: Override the element label
 */
echo $this->formButton()->render($element, 'My Content');
// <button name="my-button" type="button">My Content</button>
```

The following public methods are in addition to those inherited from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

orphan  

### FormCaptcha

TODO

Basic usage:

```php
use Zend\Captcha;
use Zend\Form\Element;

$captcha = new Element\Captcha('captcha');
$captcha
    ->setCaptcha(new Captcha\Dumb())
    ->setLabel('Please verify you are human');

// Within your view...

echo $this->formCaptcha($captcha);

// TODO
```

orphan  

### FormCheckbox

The `FormCheckbox` view helper can be used to render a `<input type="checkbox">` HTML form input. It
is meant to work with the \[Zend\\Form\\Element\\Checkbox\](zend.form.element.checkbox) element,
which provides a default input specification for validating the checkbox values.

`FormCheckbox` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Checkbox('my-checkbox');

// Within your view...

/**
 * Example #1: Default options
 */
echo $this->formCheckbox($element);
// <input type="hidden" name="my-checkbox" value="0">
// <input type="checkbox" name="my-checkbox" value="1">

/**
 * Example #2: Disable hidden element
 */
$element->setUseHiddenElement(false);
echo $this->formCheckbox($element);
// <input type="checkbox" name="my-checkbox" value="1">

/**
 * Example #3: Change checked/unchecked values
 */
$element->setUseHiddenElement(true)
        ->setUncheckedValue('no')
        ->setCheckedValue('yes');
echo $this->formCheckbox($element);
// <input type="hidden" name="my-checkbox" value="no">
// <input type="checkbox" name="my-checkbox" value="yes">
```

orphan  

### FormCollection

TODO

Basic usage:

TODO

orphan  

### FormElement

The `FormElement` view helper proxies the rendering to specific form view helpers depending on the
type of the `Zend\Form\Element` that is passed in. For instance, if the passed in element had a type
of "text", the `FormElement` helper will retrieve and use the `FormText` helper to render the
element.

Basic usage:

```php
use Zend\Form\Form;
use Zend\Form\Element;

// Within your view...

/**
 * Example #1: Render different types of form elements
 */
$textElement     = new Element\Text('my-text');
$checkboxElement = new Element\Checkbox('my-checkbox');

echo $this->formElement($textElement);
// <input type="text" name="my-text" value="">

echo $this->formElement($checkboxElement);
// <input type="hidden" name="my-checkbox" value="0">
// <input type="checkbox" name="my-checkbox" value="1">

/**
 * Example #2: Loop through form elements and render them
 */
$form = new Form();
// ...add elements and input filter to form...
$form->prepare();

// Render the opening tag
echo $this->form()->openTag($form);

// ...loop through and render the form elements...
foreach ($form as $element) {
    echo $this->formElement($element);       // <-- Magic!
    echo $this->formElementErrors($element);
}

// Render the closing tag
echo $this->form()->closeTag();
```

orphan  

### FormElementErrors

The `FormElementErrors` view helper is used to render the validation error messages of an element.

Basic usage:

```php
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

// Create a form
$form    = new Form();
$element = new Element\Text('my-text');
$form->add($element);

// Create a input
$input = new Input('my-text');
$input->setRequired(true);

$inputFilter = new InputFilter();
$inputFilter->add($input);
$form->setInputFilter($inputFilter);

// Force a failure
$form->setData(array()); // Empty data
$form->isValid();        // Not valid

// Within your view...

/**
 * Example #1: Default options
 */
echo $this->formElementErrors($element);
// <ul><li>Value is required and can&#039;t be empty</li></ul>

/**
 * Example #2: Add attributes to open format
 */
echo $this->formElementErrors($element, array('class' => 'help-inline'));
// <ul class="help-inline"><li>Value is required and can&#039;t be empty</li></ul>

/**
 * Example #3: Custom format
 */
echo $this->formElementErrors()
                ->setMessageOpenFormat('<div class="help-inline">')
                ->setMessageSeparatorString('</div><div class="help-inline">')
                ->setMessageCloseString('</div>')
                ->render($element);
// <div class="help-inline">Value is required and can&#039;t be empty</div>
```

The following public methods are in addition to those inherited from
\[Zend\\Form\\View\\Helper\\AbstractHelper\](zend.form.view.helper.abstract-helper.methods).

orphan  

### FormFile

The `FormFile` view helper can be used to render a `<input type="file">` form input. It is meant to
work with the \[Zend\\Form\\Element\\File\](zend.form.element.file) element.

`FormFile` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\File('my-file');

// Within your view...

echo $this->formFile($element);
// <input type="file" name="my-file">
```

For HTML5 multiple file uploads, the `multiple` attribute can be used. Browsers that do not support
HTML5 will default to a single upload input.

```php
use Zend\Form\Element;

$element = new Element\File('my-file');
$element->setAttribute('multiple', true);

// Within your view...

echo $this->formFile($element);
// <input type="file" name="my-file" multiple="multiple">
```

orphan  

### FormHidden

The `FormHidden` view helper can be used to render a `<input type="hidden">` HTML form input. It is
meant to work with the \[Zend\\Form\\Element\\Hidden\](zend.form.element.hidden) element.

`FormHidden` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Hidden('my-hidden');
$element->setValue('foo');

// Within your view...

echo $this->formHidden($element);
// <input type="hidden" name="my-hidden" value="foo">
```

orphan  

### FormImage

The `FormImage` view helper can be used to render a `<input type="image">` HTML form input. It is
meant to work with the \[Zend\\Form\\Element\\Image\](zend.form.element.image) element.

`FormImage` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Image('my-image');
$element->setAttribute('src', '/img/my-pic.png');

// Within your view...

echo $this->formImage($element);
// <input type="image" name="my-image" src="/img/my-pic.png">
```

orphan  

### FormInput

The `FormInput` view helper is used to render a `<input>` HTML form input tag. It acts as a base
class for all of the specifically typed form input helpers (FormText, FormCheckbox, FormSubmit,
etc.), and is not suggested for direct use.

It contains a general map of valid tag attributes and types for attribute filtering. Each subclass
of `FormInput` implements it's own specific map of valid tag attributes.

The following public methods are in addition to those inherited from
\[Zend\\Form\\View\\Helper\\AbstractHelper\](zend.form.view.helper.abstract-helper.methods).

orphan  

### FormLabel

The `FormLabel` view helper is used to render a `<label>` HTML element and its attributes. If you
have a `Zend\I18n\Translator\Translator` attached, `FormLabel` will translate the label contents
during it's rendering.

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Text('my-text');
$element->setLabel('Label')
        ->setAttribute('id', 'text-id')
        ->setLabelAttributes(array('class' => 'control-label'));

// Within your view...

/**
 * Example #1: Render label in one shot
 */
echo $this->formLabel($element);
// <label class="control-label" for="text-id">Label</label>

echo $this->formLabel($element, $this->formText($element));
// <label class="control-label" for="text-id">Label<input type="text" name="my-text"></label>

echo $this->formLabel($element, $this->formText($element), 'append');
// <label class="control-label" for="text-id"><input type="text" name="my-text">Label</label>

/**
 * Example #2: Render label in separate steps
 */
// Render the opening tag
echo $this->formLabel()->openTag($element);
// <label class="control-label" for="text-id">

// Render the closing tag
echo $this->formLabel()->closeTag();
// </label>

/**
 * Example #3: Render html label after toggling off escape
 */
$element->setLabel('<abbr title="Completely Automated Public Turing test to tell Computers and
Humans Apart">CAPTCHA</abbr>');
$element->setLabelOptions(array('disable_html_escape' => true));
echo $this->formLabel($element);
// <label class="control-label" for="text-id">
//     <abbr title="Completely Automated Public Turing test to tell Computers and Humans
Apart">CAPTCHA</abbr>
// </label>
```

> ## Note
HTML escape only applies to the `Element::$label` property, not to the helper `$labelContent`
parameter.

Attaching a translator and setting a text domain:

```php
// Setting a translator
$this->formLabel()->setTranslator($translator);

// Setting a text domain
$this->formLabel()->setTranslatorTextDomain('my-text-domain');

// Setting both
$this->formLabel()->setTranslator($translator, 'my-text-domain');
```

> ## Note
If you have a translator in the Service Manager under the key, 'translator', the view helper plugin
manager will automatically attach the translator to the FormLabel view helper. See
`Zend\View\HelperPluginManager::injectTranslator()` for more information.

The following public methods are in addition to those inherited from
\[Zend\\Form\\View\\Helper\\AbstractHelper\](zend.form.view.helper.abstract-helper.methods).

orphan  

### FormMultiCheckbox

The `FormMultiCheckbox` view helper can be used to render a group `<input type="checkbox">` HTML
form inputs. It is meant to work with the
\[Zend\\Form\\Element\\MultiCheckbox\](zend.form.element.multicheckbox) element, which provides a
default input specification for validating a multi checkbox.

`FormMultiCheckbox` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\MultiCheckbox('my-multicheckbox');
$element->setValueOptions(array(
   '0' => 'Apple',
   '1' => 'Orange',
   '2' => 'Lemon',
));

// Within your view...

/**
 * Example #1: using the default label placement
 */
echo $this->formMultiCheckbox($element);
// <label><input type="checkbox" name="my-multicheckbox[]" value="0">Apple</label>
// <label><input type="checkbox" name="my-multicheckbox[]" value="1">Orange</label>
// <label><input type="checkbox" name="my-multicheckbox[]" value="2">Lemon</label>

/**
 * Example #2: using the prepend label placement
 */
echo $this->formMultiCheckbox($element, 'prepend');
// <label>Apple<input type="checkbox" name="my-multicheckbox[]" value="0"></label>
// <label>Orange<input type="checkbox" name="my-multicheckbox[]" value="1"></label>
// <label>Lemon<input type="checkbox" name="my-multicheckbox[]" value="2"></label>
```

orphan  

### FormPassword

The `FormPassword` view helper can be used to render a `<input type="password">` HTML form input. It
is meant to work with the \[Zend\\Form\\Element\\Password\](zend.form.element.password) element.

`FormPassword` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Password('my-password');

// Within your view...
echo $this->formPassword($element);
```

Output:

```php
<input type="password" name="my-password" value="">
```

orphan  

### FormRadio

The `FormRadio` view helper can be used to render a group `<input type="radio">` HTML form inputs.
It is meant to work with the \[Zend\\Form\\Element\\Radio\](zend.form.element.radio) element, which
provides a default input specification for validating a radio.

`FormRadio` extends from
\[Zend\\Form\\View\\Helper\\FormMultiCheckbox\](zend.form.view.helper.form-multicheckbox).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Radio('gender');
$element->setValueOptions(array(
   '0' => 'Male',
   '1' => 'Female',
));

// Within your view...

/**
 * Example #1: using the default label placement
 */
echo $this->formRadio($element);
// <label><input type="radio" name="gender[]" value="0">Male</label>
// <label><input type="radio" name="gender[]" value="1">Female</label>

/**
 * Example #2: using the prepend label placement
 */
echo $this->formRadio($element, 'prepend');
// <label>Male<input type="checkbox" name="gender[]" value="0"></label>
// <label>Female<input type="checkbox" name="gender[]" value="1"></label>
```

orphan  

### FormReset

The `FormReset` view helper can be used to render a `<input type="reset">` HTML form input.

`FormText` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element('my-reset');
$element->setAttribute('value', 'Reset');

// Within your view...
echo $this->formReset($element);
```

Output:

```php
<input type="reset" name="my-reset" value="Reset">
```

orphan  

### FormRow

The `FormRow` view helper is in turn used by `Form` view helper to render each row of a form,
nevertheless it can be use stand-alone. A form row usually consists of the output produced by the
helper specific to an input, plus its label and errors, if any.

`FormRow` handles different rendering options, having elements wrapped by the &lt;label&gt; HTML
block by default, but also allowing to render them in separate blocks when the element has an `id`
attribute specified, thus preserving browser usability features in any case.

Other options involve label positioning, escaping, toggling errors and using custom partial
templates. Please check out `Zend\Form\View\Helper\FormRow` method API for more details.

Usage:

```php
/**
 * inside view template
 *
 * @var \Zend\View\Renderer\PhpRenderer $this
 * @var \Zend\Form\Form $form
 */

// Prepare the form
$form->prepare();

// Render the opening tag
echo $this->form()->openTag($form);

/** @var \Zend\Form\Element\Text $element */
$element = $form->get('some_element');
$element->setLabel('Some Label');

// Render 'some_element' label, input, and errors if any
echo $this->formRow($element);
// i.e. <label><span>Some Label</span><input type="text" name="some_element" value=""></label>

// Altering label position
echo $this->formRow($element, 'append');
// i.e. <label><input type="text" name="some_element" value=""><span>Some Label</span></label>

// Setting the 'id' attribute will result in a separated label rather than a wrapping one
$element->setAttribute('id', 'element_id');
echo $this->formRow($element);
// i.e. <label for="element_id">Some Label</label><input type="text" name="some_element"
id="element_id" value="">

// Turn off escaping for HTML labels
$element->setLabel('<abbr title="Completely Automated Public Turing test to tell Computers and
Humans Apart">CAPTCHA</abbr>');
$element->setLabelOptions(array('disable_html_escape' => true));
// i.e.
// <label>
//   <span>
//       <abbr title="Completely Automated Public Turing test to tell Computers and Humans
Apart">CAPTCHA</abbr>
//   </span>
//   <input type="text" name="some_element" value="">
// </label>

// Render the closing tag
echo $this->form()->closeTag();
```

> ## Note
Label content is escaped by default

orphan  

### FormSelect

The `FormSelect` view helper can be used to render a group `<input type="select">` HTML form input.
It is meant to work with the \[Zend\\Form\\Element\\Select\](zend.form.element.select) element,
which provides a default input specification for validating a select.

`FormSelect` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Select('language');
$element->setValueOptions(array(
   '0' => 'French',
   '1' => 'English',
   '2' => 'Japanese',
   '3' => 'Chinese'
));

// Within your view...

/**
 * Example
 */
echo $this->formSelect($element);
```

orphan  

### FormSubmit

The `FormSubmit` view helper can be used to render a `<input type="submit">` HTML form input. It is
meant to work with the \[Zend\\Form\\Element\\Submit\](zend.form.element.submit) element.

`FormSubmit` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Submit('my-submit');

// Within your view...
echo $this->formSubmit($element);
```

Output:

```php
<input type="submit" name="my-submit" value="">
```

orphan  

### FormText

The `FormText` view helper can be used to render a `<input type="text">` HTML form input. It is
meant to work with the \[Zend\\Form\\Element\\Text\](zend.form.element.text) element.

`FormText` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Text('my-text');

// Within your view...
echo $this->formText($element);
```

Output:

```php
<input type="text" name="my-text" value="">
```

orphan  

### FormTextarea

The `FormTextarea` view helper can be used to render a `<textarea></textarea>` HTML form input. It
is meant to work with the \[Zend\\Form\\Element\\Textarea\](zend.form.element.textarea) element.

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Textarea('my-textarea');

// Within your view...
echo $this->formTextarea($element);
```

Output:

```php
<textarea name="my-textarea"></textarea>
```

orphan  

### AbstractHelper

The `AbstractHelper` is used as a base abstract class for Form view helpers, providing methods for
validating form HTML attributes, as well as controlling the doctype and character encoding.
`AbstractHelper` also extends from `Zend\I18n\View\Helper\AbstractTranslatorHelper` which provides
an implementation for the `Zend\I18n\Translator\TranslatorAwareInterface` that allows setting a
translator and text domain.

The following public methods are in addition to the inherited methods of
Zend\\\\I18n\\\\View\\\\Helper\\\\AbstractTranslatorHelper
&lt;zend.i18n.view.helper.abstract-translator-helper.methods&gt;.

## HTML5 Helpers

orphan  

### FormColor

The `FormColor` view helper can be used to render a `<input type="color">` HTML5 form input. It is
meant to work with the \[Zend\\Form\\Element\\Color\](zend.form.element.color) element.

`FormColor` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Color('my-color');

// Within your view...
echo $this->formColor($element);
```

Output:

```php
<input type="color" name="my-color" value="">
```

orphan  

### FormDate

The `FormDate` view helper can be used to render a `<input type="date">` HTML5 form input. It is
meant to work with the \[Zend\\Form\\Element\\Date\](zend.form.element.date) element, which provides
a default input specification for validating HTML5 date values.

`FormDate` extends from
\[Zend\\Form\\View\\Helper\\FormDateTime\](zend.form.view.helper.form-date-time).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Date('my-date');

// Within your view...

echo $this->formDate($element);
// <input type="date" name="my-date" value="">
```

orphan  

### FormDateTime

The `FormDateTime` view helper can be used to render a `<input type="datetime">` HTML5 form input.
It is meant to work with the \[Zend\\Form\\Element\\DateTime\](zend.form.element.date-time) element,
which provides a default input specification for validating HTML5 datetime values.

`FormDateTime` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\DateTime('my-datetime');

// Within your view...

echo $this->formDateTime($element);
// <input type="datetime" name="my-datetime" value="">
```

orphan  

### FormDateTimeLocal

The `FormDateTimeLocal` view helper can be used to render a `<input type="datetime-local">` HTML5
form input. It is meant to work with the
\[Zend\\Form\\Element\\DateTimeLocal\](zend.form.element.date-time-local) element, which provides a
default input specification for validating HTML5 datetime values.

`FormDateTimeLocal` extends from
\[Zend\\Form\\View\\Helper\\FormDateTime\](zend.form.view.helper.form-date-time).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\DateTimeLocal('my-datetime');

// Within your view...

echo $this->formDateTimeLocal($element);
// <input type="datetime-local" name="my-datetime" value="">
```

orphan  

### FormEmail

The `FormEmail` view helper can be used to render a `<input type="email">` HTML5 form input. It is
meant to work with the \[Zend\\Form\\Element\\Email\](zend.form.element.email) element, which
provides a default input specification with an email validator.

`FormEmail` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Email('my-email');

// Within your view...

echo $this->formEmail($element);
// <input type="email" name="my-email" value="">
```

orphan  

### FormMonth

The `FormMonth` view helper can be used to render a `<input type="month">` HTML5 form input. It is
meant to work with the \[Zend\\Form\\Element\\Month\](zend.form.element.month) element, which
provides a default input specification for validating HTML5 date values.

`FormMonth` extends from
\[Zend\\Form\\View\\Helper\\FormDateTime\](zend.form.view.helper.form-date-time).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Month('my-month');

// Within your view...

echo $this->formMonth($element);
// <input type="month" name="my-month" value="">
```

orphan  

### FormNumber

The `FormNumber` view helper can be used to render a `<input type="number">` HTML form input. It is
meant to work with the \[Zend\\Form\\Element\\Number\](zend.form.element.number) element, which
provides a default input specification for validating numerical values.

`FormNumber` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Number('my-number');

// Within your view...
echo $this->formNumber($element);
```

Output:

```php
<input type="number" name="my-number" value="">
```

Usage of `min`, `max` and `step` attributes:

```php
use Zend\Form\Element;

$element = new Element\Number('my-number');
$element->setAttributes(
    array(
        'min'  => 5,
        'max'  => 20,
        'step' => 0.5,
    )
);
$element->setValue(12);

// Within your view...
echo $this->formNumber($element);
```

Output:

```php
<input type="number" name="my-number" min="5" max="20" step="0.5" value="12">
```

orphan  

### FormRange

The `FormRange` view helper can be used to render a `<input type="range">` HTML form input. It is
meant to work with the \[Zend\\Form\\Element\\Range\](zend.form.element.range) element, which
provides a default input specification for validating numerical values.

`FormRange` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Range('my-range');

// Within your view...
echo $this->formRange($element);
```

Output:

```php
<input type="range" name="my-range" value="">
```

Usage of `min`, `max` and `step` attributes:

```php
use Zend\Form\Element;

$element = new Element\Range('my-range');
$element->setAttributes(
    array(
        'min'  => 0,
        'max'  => 100,
        'step' => 5,
    )
);
$element->setValue(20);

// Within your view...
echo $this->formRange($element);
```

Output:

```php
<input type="range" name="my-range" min="0" max="100" step="5" value="20">
```

orphan  

### FormSearch

The `FormSearch` view helper can be used to render a `<input type="search">` HTML5 form input.

`FormSearch` extends from \[Zend\\Form\\View\\Helper\\FormText\](zend.form.view.helper.form-text).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element('my-search');

// Within your view...
echo $this->formSearch($element);
```

Output:

```php
<input type="search" name="my-search" value="">
```

orphan  

### FormTel

The `FormTel` view helper can be used to render a `<input type="tel">` HTML5 form input.

`FormTel` extends from \[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element('my-tel');

// Within your view...
echo $this->formTel($element);
```

Output:

```php
<input type="tel" name="my-tel" value="">
```

orphan  

### FormTime

The `FormTime` view helper can be used to render a `<input type="time">` HTML5 form input. It is
meant to work with the \[Zend\\Form\\Element\\Time\](zend.form.element.time) element, which provides
a default input specification for validating HTML5 time values.

`FormTime` extends from
\[Zend\\Form\\View\\Helper\\FormDateTime\](zend.form.view.helper.form-date-time).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Time('my-time');

// Within your view...

echo $this->formTime($element);
// <input type="time" name="my-time" value="">
```

orphan  

### FormUrl

The `FormUrl` view helper can be used to render a `<input type="url">` HTML form input. It is meant
to work with the \[Zend\\Form\\Element\\Url\](zend.form.element.url) element, which provides a
default input specification with an URL validator.

`FormUrl` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input.methods).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Url('my-url');

// Within your view...
echo $this->formUrl($element);
```

Output:

```php
<input type="url" name="my-url" value="">
```

Usage of custom regular expression pattern:

```php
use Zend\Form\Element;

$element = new Element\Url('my-url');
$element->setAttribute('pattern', 'https?://.+');

// Within your view...
echo $this->formUrl($element);
```

Output:

```php
<input type="url" name="my-url" pattern="https?://.+" value="">
```

orphan  

### FormWeek

The `FormWeek` view helper can be used to render a `<input type="week">` HTML5 form input. It is
meant to work with the \[Zend\\Form\\Element\\Week\](zend.form.element.week) element, which provides
a default input specification for validating HTML5 week values.

`FormWeek` extends from
\[Zend\\Form\\View\\Helper\\FormDateTime\](zend.form.view.helper.form-date-time).

Basic usage:

```php
use Zend\Form\Element;

$element = new Element\Week('my-week');

// Within your view...
echo $this->formWeek($element);
```

Output:

```php
<input type="week" name="my-week" value="">
```

Usage of `min`, `max` and `step` attributes:

```php
use Zend\Form\Element;

$element = new Element\Week('my-week');
$element->setAttributes(
    array(
        'min'  => '2012-W01',
        'max'  => '2020-W01',
        'step' => 2, // weeks; default step interval is 1 week
    )
);
$element->setValue('2014-W10');

// Within your view...
echo $this->formWeek($element);
```

Output:

```php
<input type="week" name="my-week" min="2012-W01" max="2020-W01" step="2" value="2014-W10">
```

## File Upload Progress Helpers

orphan  

### FormFileApcProgress

The `FormFileApcProgress` view helper can be used to render a `<input type="hidden" ...>` with a
progress ID value used by the APC File Upload Progress feature. The APC php module is required for
this view helper to work. Unlike other Form view helpers, the `FormFileSessionProgress` helper does
not accept a Form Element as a parameter.

An `id` attribute with a value of `"progress_key"` will automatically be added.

> ## Warning
The view helper **must** be rendered *before* the file input in the form, or upload progress will
not work correctly.

Best used with the \[Zend\\ProgressBar\\Upload\\ApcProgress\](zend.progress-bar.upload.apc-progress)
handler.

See the `apc.rfc1867` ini setting in the [APC
Configuration](http://php.net/manual/en/apc.configuration.php) documentation for more information.

`FormFileApcProgress` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
// Within your view...

echo $this->formFileApcProgress();
// <input type="hidden" id="progress_key" name="APC_UPLOAD_PROGRESS" value="12345abcde">
```

orphan  

### FormFileSessionProgress

The `FormFileSessionProgress` view helper can be used to render a `<input type="hidden" ...>` which
can be used by the PHP 5.4 File Upload Session Progress feature. PHP 5.4 is required for this view
helper to work. Unlike other Form view helpers, the `FormFileSessionProgress` helper does not accept
a Form Element as a parameter.

An `id` attribute with a value of `"progress_key"` will automatically be added.

> ## Warning
The view helper **must** be rendered *before* the file input in the form, or upload progress will
not work correctly.

Best used with the
\[Zend\\ProgressBar\\Upload\\SessionProgress\](zend.progress-bar.upload.session-progress) handler.

See the [Session Upload Progress](http://php.net/manual/en/session.upload-progress.php) in the PHP
documentation for more information.

`FormFileSessionProgress` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
// Within your view...

echo $this->formFileSessionProgress();
// <input type="hidden" id="progress_key" name="PHP_SESSION_UPLOAD_PROGRESS" value="12345abcde">
```

orphan  

### FormFileUploadProgress

The `FormFileUploadProgress` view helper can be used to render a `<input type="hidden" ...>` which
can be used by the PECL uploadprogress extension. Unlike other Form view helpers, the
`FormFileUploadProgress` helper does not accept a Form Element as a parameter.

An `id` attribute with a value of `"progress_key"` will automatically be added.

> ## Warning
The view helper **must** be rendered *before* the file input in the form, or upload progress will
not work correctly.

Best used with the
\[Zend\\ProgressBar\\Upload\\UploadProgress\](zend.progress-bar.upload.upload-progress) handler.

See the [PECL uploadprogress extension](http://pecl.php.net/package/uploadprogress) for more
information.

`FormFileUploadProgress` extends from
\[Zend\\Form\\View\\Helper\\FormInput\](zend.form.view.helper.form-input).

Basic usage:

```php
// Within your view...

echo $this->formFileSessionProgress();
// <input type="hidden" id="progress_key" name="UPLOAD_IDENTIFIER" value="12345abcde">
```
