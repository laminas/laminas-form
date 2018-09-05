<?php
/**
 * @see       https://github.com/zendframework/zend-form for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-form/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Form\View;

use IntlDateFormatter;
use Zend\Form\ElementInterface;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper\Captcha\Dumb;
use Zend\Form\View\Helper\Captcha\Figlet;
use Zend\Form\View\Helper\Captcha\Image;
use Zend\Form\View\Helper\Captcha\ReCaptcha;
use Zend\Form\View\Helper\Form;
use Zend\Form\View\Helper\FormButton;
use Zend\Form\View\Helper\FormCaptcha;
use Zend\Form\View\Helper\FormCheckbox;
use Zend\Form\View\Helper\FormCollection;
use Zend\Form\View\Helper\FormColor;
use Zend\Form\View\Helper\FormDate;
use Zend\Form\View\Helper\FormDateSelect;
use Zend\Form\View\Helper\FormDateTime;
use Zend\Form\View\Helper\FormDateTimeLocal;
use Zend\Form\View\Helper\FormDateTimeSelect;
use Zend\Form\View\Helper\FormElement;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\Form\View\Helper\FormEmail;
use Zend\Form\View\Helper\FormFile;
use Zend\Form\View\Helper\FormHidden;
use Zend\Form\View\Helper\FormImage;
use Zend\Form\View\Helper\FormInput;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\View\Helper\FormMonth;
use Zend\Form\View\Helper\FormMonthSelect;
use Zend\Form\View\Helper\FormMultiCheckbox;
use Zend\Form\View\Helper\FormNumber;
use Zend\Form\View\Helper\FormPassword;
use Zend\Form\View\Helper\FormRadio;
use Zend\Form\View\Helper\FormRange;
use Zend\Form\View\Helper\FormReset;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormSearch;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormSubmit;
use Zend\Form\View\Helper\FormTel;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormTextarea;
use Zend\Form\View\Helper\FormTime;
use Zend\Form\View\Helper\FormUrl;
use Zend\Form\View\Helper\FormWeek;

// @codingStandardsIgnoreStart

/**
 * Trait HelperTrait
 *
 * The trait provides convenience methods for view helpers,
 * defined by the zend-form component. It is designed to be used
 * for type-hinting $this variable inside zend-view templates via doc blocks.
 *
 * The base class is PhpRenderer, followed by the helper trait from
 * the zend-form component. However, multiple helper traits from different
 * Zend components can be chained afterwards.
 *
 * @example @var \Zend\View\Renderer\PhpRenderer|\Zend\Form\View\HelperTrait $this
 *
 * @method string|Form form(FormInterface $form = null)
 * @method string|FormButton formButton(ElementInterface $element = null, $buttonContent = null)
 * @method string|FormCaptcha formCaptcha(ElementInterface $element = null)
 * @method string|Dumb formCaptchaDumb(ElementInterface $element = null)
 * @method string|Figlet formCaptchaFiglet(ElementInterface $element = null)
 * @method string|Image formCaptchaImage(ElementInterface $element = null)
 * @method string|ReCaptcha formCaptchaRecaptcha(ElementInterface $element = null)
 * @method string|FormCheckbox formCheckbox(ElementInterface $element = null)
 * @method string|FormCollection formCollection(ElementInterface $element = null, $wrap = true)
 * @method string|FormColor formColor(ElementInterface $element = null)
 * @method string|FormDate formDate(ElementInterface $element = null)
 * @method string|FormDateTime formDateTime(ElementInterface $element = null)
 * @method string|FormDateTimeLocal formDateTimeLocal(ElementInterface $element = null)
 * @method string|FormDateTimeSelect formDateTimeSelect(ElementInterface $element = null, $dateType = IntlDateFormatter::LONG, $timeType = IntlDateFormatter::LONG, $locale = null)
 * @method string|FormDateSelect formDateSelect(ElementInterface $element = null, $dateType = IntlDateFormatter::LONG, $locale = null)
 * @method string|FormElement formElement(ElementInterface $element = null)
 * @method string|FormElementErrors formElementErrors(ElementInterface $element = null, array $attributes = [])
 * @method string|FormEmail formEmail(ElementInterface $element = null)
 * @method string|FormFile formFile(ElementInterface $element = null)
 * @method string formFileApcProgress(ElementInterface $element = null)
 * @method string formFileSessionProgress(ElementInterface $element = null)
 * @method string formFileUploadProgress(ElementInterface $element = null)
 * @method string|FormHidden formHidden(ElementInterface $element = null)
 * @method string|FormImage formImage(ElementInterface $element = null)
 * @method string|FormInput formInput(ElementInterface $element = null)
 * @method string|FormLabel formLabel(ElementInterface $element = null, $labelContent = null, $position = null)
 * @method string|FormMonth formMonth(ElementInterface $element = null)
 * @method string|FormMonthSelect formMonthSelect(ElementInterface $element = null, $dateType = IntlDateFormatter::LONG, $locale = null)
 * @method string|FormMultiCheckbox formMultiCheckbox(ElementInterface $element = null, $labelPosition = null)
 * @method string|FormNumber formNumber(ElementInterface $element = null)
 * @method string|FormPassword formPassword(ElementInterface $element = null)
 * @method string|FormRadio formRadio(ElementInterface $element = null, $labelPosition = null)
 * @method string|FormRange formRange(ElementInterface $element = null)
 * @method string|FormReset formReset(ElementInterface $element = null)
 * @method string|FormRow formRow(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
 * @method string|FormSearch formSearch(ElementInterface $element = null)
 * @method string|FormSelect formSelect(ElementInterface $element = null)
 * @method string|FormSubmit formSubmit(ElementInterface $element = null)
 * @method string|FormTel formTel(ElementInterface $element = null)
 * @method string|FormText formText(ElementInterface $element = null)
 * @method string|FormTextarea formTextarea(ElementInterface $element = null)
 * @method string|FormTime formTime(ElementInterface $element = null)
 * @method string|FormUrl formUrl(ElementInterface $element = null)
 * @method string|FormWeek formWeek(ElementInterface $element = null)
 */
trait HelperTrait
{
}
// @codingStandardsIgnoreEnd
