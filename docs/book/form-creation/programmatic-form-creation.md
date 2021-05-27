# Programmatic Form Creation

The following example demonstrates element, fieldset, and form creation, and how
they are wired together.

```php
use Laminas\Captcha;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;

// Create a text element to capture the user name:
$name = new Element('name');
$name->setLabel('Your name');
$name->setAttributes([
    'type' => 'text',
]);

// Create a text element to capture the user email address:
$email = new Element\Email('email');
$email->setLabel('Your email address');

// Create a text element to capture the message subject:
$subject = new Element('subject');
$subject->setLabel('Subject');
$subject->setAttributes([
    'type' => 'text',
]);

// Create a textarea element to capture a message:
$message = new Element\Textarea('message');
$message->setLabel('Message');

// Create a CAPTCHA:
$captcha = new Element\Captcha('captcha');
$captcha->setCaptcha(new Captcha\Dumb());
$captcha->setLabel('Please verify you are human');

// Create a CSRF token:
$csrf = new Element\Csrf('security');

// Create a submit button:
$send = new Element('send');
$send->setValue('Submit');
$send->setAttributes([
    'type' => 'submit',
]);

// Create the form and add all elements:
$form = new Form('contact');
$form->add($name);
$form->add($email);
$form->add($subject);
$form->add($message);
$form->add($captcha);
$form->add($csrf);
$form->add($send);

// Create an input for the "name" element:
$nameInput = new Input('name');

/* ... configure the input, and create and configure all others ... */

// Create the input filter:
$inputFilter = new InputFilter();

// Attach inputs:
$inputFilter->add($nameInput);
/* ... */

// Attach the input filter to the form:
$form->setInputFilter($inputFilter);
```

As a demonstration of fieldsets, let's alter the above slightly. We'll create
two fieldsets, one for the sender information, and another for the message
details.

```php
// Create the fieldset for sender details:
$sender = new Fieldset('sender');
$sender->add($name);
$sender->add($email);

// Create the fieldset for message details:
$details = new Fieldset('details');
$details->add($subject);
$details->add($message);

$form = new Form('contact');
$form->add($sender);
$form->add($details);
$form->add($captcha);
$form->add($csrf);
$form->add($send);
```

This manual approach gives maximum flexibility over form creation; however, it
comes at the expense of verbosity. In the next section, we'll look at another
approach.
