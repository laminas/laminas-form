# Factory-Backed Form Extension

The default `Form` implementation is backed by the `Factory`. This allows you to
extend it, and define your form internally. This has the benefit of allowing a
mixture of programmatic and factory-backed creation, as well as defining a form
for re-use in your application.

```php
namespace Contact;

use Laminas\Captcha\AdapterInterface as CaptchaAdapter;
use Laminas\Form\Element;
use Laminas\Form\Form;

class ContactForm extends Form
{
    protected $captcha;

    public function __construct(CaptchaAdapter $captcha)
    {
        parent::__construct();

        $this->captcha = $captcha;

        // add() can take an Element/Fieldset instance, or a specification, from
        // which the appropriate object will be built.
        $this->add([
            'name' => 'name',
            'options' => [
                'label' => 'Your name',
            ],
            'type'  => 'Text',
        ]);
        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'Your email address',
            ],
        ]);
        $this->add([
            'name' => 'subject',
            'options' => [
                'label' => 'Subject',
            ],
            'type'  => 'Text',
        ]);
        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'message',
            'options' => [
                'label' => 'Message',
            ],
        ]);
        $this->add([
            'type' => Element\Captcha::class,
            'name' => 'captcha',
            'options' => [
                'label' => 'Please verify you are human.',
                'captcha' => $this->captcha,
            ],
        ]);
        $this->add(new Element\Csrf('security'));
        $this->add([
            'name' => 'send',
            'type'  => 'Submit',
            'attributes' => [
                'value' => 'Submit',
            ],
        ]);

        // We could also define the input filter here, or
        // lazy-create it in the getInputFilter() method.
    }
}
```

In the above example, elements are added in the constructor. This is done to
allow altering and/or configuring either the form or input filter factory
instances, which could then have bearing on how elements, inputs, etc. are
created. In this case, it also allows injection of the CAPTCHA adapter, allowing
us to configure it elsewhere in our application and inject it into the form.
