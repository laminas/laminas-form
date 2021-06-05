# FormCaptcha

`Laminas\Form\View\Helper\FormCaptcha` will render a CAPTCHA as defined in a
[Captcha](../element/captcha.md) element.

## Basic usage

```php
use Laminas\Captcha;
use Laminas\Form\Element;

$captcha = new Element\Captcha('captcha');
$captcha->setCaptcha(new Captcha\Dumb());
$captcha->setLabel('Please verify you are human');

// Within your view...

echo $this->formCaptcha($captcha);
```
