# Month

`Laminas\Form\Element\Month` is meant to be paired with the
[FormMonth](../helper/form-month.md) helper for
[HTML5 inputs with type "month"](http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#month-state-%28type=month%29).
This element adds filters and validators to its input filter specification in
order to validate HTML5 month input values on the server.

## Basic Usage

This element automatically adds a `type` attribute of value `month`.

```php
use Laminas\Form\Element;
use Laminas\Form\Form;

$month = new Element\Month('month');
$month->setLabel('Month');
$month->setAttributes([
    'min'  => '2012-01',
    'max'  => '2020-01',
    'step' => '1', // months; default step interval is 1 month
]);

$form = new Form('my-form');
$form->add($month);
```

Using array notation:

```php
use Laminas\Form\Element;
use Laminas\Form\Form;

$form = new Form('my-form');
$form->add([
    'type' => Element\Month::class,
    'name' => 'month',
    'options' => [
        'label' => 'Month',
    ],
    'attributes' => [
        'min' => '2012-12',
        'max' => '2020-01',
        'step' => '1', // months; default step interval is 1 month
    ],
]);
```

> ### Set all attributes before calling prepare
>
> The `min`, `max`, and `step` attributes should be set prior to calling
> `Laminas\Form::prepare()`. Otherwise, the default input specification for the
> element may not contain the correct validation rules.

## Public Methods

The following methods are specific to the `Month` element; all other methods
inherited from the [parent `DateTime` class](date-time.md#public-methods) are also
available.

Method signature                  | Description
--------------------------------- | -----------
`getInputSpecification() : array` | Returns an input filter specification, which includes `Laminas\Filter\StringTrim`, and will add the appropriate validators based on the values from the `min`, `max`, and `step` attributes. See the [DateTime element documentation](date-time.md#public-methods) for more information.  One difference from `Laminas\Form\Element\DateTime` is that the `Laminas\Validator\DateStep` validator will expect the `step` attribute to use an interval of months (default is 1 month).
