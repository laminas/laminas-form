<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\View;

use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Service manager configuration for form view helpers
 */
class HelperConfig implements ConfigInterface
{
    /**
     * Pre-aliased view helpers
     *
     * @var array
     */
    protected $aliases = [
        'form'                       => Helper\Form::class,
        'Form'                       => Helper\Form::class,
        'formbutton'                 => Helper\FormButton::class,
        'form_button'                => Helper\FormButton::class,
        'formButton'                 => Helper\FormButton::class,
        'FormButton'                 => Helper\FormButton::class,
        'formcaptcha'                => Helper\FormCaptcha::class,
        'form_captcha'               => Helper\FormCaptcha::class,
        'formCaptcha'                => Helper\FormCaptcha::class,
        'FormCaptcha'                => Helper\FormCaptcha::class,
        'captchadumb'                => Helper\Captcha\Dumb::class,
        'captcha_dumb'               => Helper\Captcha\Dumb::class,
        // weird alias used by Zend\Captcha
        'captcha/dumb'               => Helper\Captcha\Dumb::class,
        'CaptchaDumb'                => Helper\Captcha\Dumb::class,
        'captchaDumb'                => Helper\Captcha\Dumb::class,
        'formcaptchadumb'            => Helper\Captcha\Dumb::class,
        'form_captcha_dumb'          => Helper\Captcha\Dumb::class,
        'formCaptchaDumb'            => Helper\Captcha\Dumb::class,
        'FormCaptchaDumb'            => Helper\Captcha\Dumb::class,
        'captchafiglet'              => Helper\Captcha\Figlet::class,
        // weird alias used by Zend\Captcha
        'captcha/figlet'             => Helper\Captcha\Figlet::class,
        'captcha_figlet'             => Helper\Captcha\Figlet::class,
        'captchaFiglet'              => Helper\Captcha\Figlet::class,
        'CaptchaFiglet'              => Helper\Captcha\Figlet::class,
        'formcaptchafiglet'          => Helper\Captcha\Figlet::class,
        'form_captcha_figlet'        => Helper\Captcha\Figlet::class,
        'formCaptchaFiglet'          => Helper\Captcha\Figlet::class,
        'FormCaptchaFiglet'          => Helper\Captcha\Figlet::class,
        'captchaimage'               => Helper\Captcha\Image::class,
        // weird alias used by Zend\Captcha
        'captcha/image'              => Helper\Captcha\Image::class,
        'captcha_image'              => Helper\Captcha\Image::class,
        'captchaImage'               => Helper\Captcha\Image::class,
        'CaptchaImage'               => Helper\Captcha\Image::class,
        'formcaptchaimage'           => Helper\Captcha\Image::class,
        'form_captcha_image'         => Helper\Captcha\Image::class,
        'formCaptchaImage'           => Helper\Captcha\Image::class,
        'FormCaptchaImage'           => Helper\Captcha\Image::class,
        'captcharecaptcha'           => Helper\Captcha\ReCaptcha::class,
        // weird alias used by Zend\Captcha
        'captcha/recaptcha'          => Helper\Captcha\ReCaptcha::class,
        'captcha_recaptcha'          => Helper\Captcha\ReCaptcha::class,
        'captchaRecaptcha'           => Helper\Captcha\ReCaptcha::class,
        'CaptchaRecaptcha'           => Helper\Captcha\ReCaptcha::class,
        'formcaptcharecaptcha'       => Helper\Captcha\ReCaptcha::class,
        'form_captcha_recaptcha'     => Helper\Captcha\ReCaptcha::class,
        'formCaptchaRecaptcha'       => Helper\Captcha\ReCaptcha::class,
        'FormCaptchaRecaptcha'       => Helper\Captcha\ReCaptcha::class,
        'formcheckbox'               => Helper\FormCheckbox::class,
        'form_checkbox'              => Helper\FormCheckbox::class,
        'formCheckbox'               => Helper\FormCheckbox::class,
        'FormCheckbox'               => Helper\FormCheckbox::class,
        'formcollection'             => Helper\FormCollection::class,
        'form_collection'            => Helper\FormCollection::class,
        'formCollection'             => Helper\FormCollection::class,
        'FormCollection'             => Helper\FormCollection::class,
        'formcolor'                  => Helper\FormColor::class,
        'form_color'                 => Helper\FormColor::class,
        'formColor'                  => Helper\FormColor::class,
        'FormColor'                  => Helper\FormColor::class,
        'formdate'                   => Helper\FormDate::class,
        'form_date'                  => Helper\FormDate::class,
        'formDate'                   => Helper\FormDate::class,
        'FormDate'                   => Helper\FormDate::class,
        'formdatetime'               => Helper\FormDateTime::class,
        'form_date_time'             => Helper\FormDateTime::class,
        'formDateTime'               => Helper\FormDateTime::class,
        'FormDateTime'               => Helper\FormDateTime::class,
        'formdatetimelocal'          => Helper\FormDateTimeLocal::class,
        'form_date_time_local'       => Helper\FormDateTimeLocal::class,
        'formDateTimeLocal'          => Helper\FormDateTimeLocal::class,
        'FormDateTimeLocal'          => Helper\FormDateTimeLocal::class,
        'formdatetimeselect'         => Helper\FormDateTimeSelect::class,
        'form_date_time_select'      => Helper\FormDateTimeSelect::class,
        'formDateTimeSelect'         => Helper\FormDateTimeSelect::class,
        'FormDateTimeSelect'         => Helper\FormDateTimeSelect::class,
        'formdateselect'             => Helper\FormDateSelect::class,
        'form_date_select'           => Helper\FormDateSelect::class,
        'formDateSelect'             => Helper\FormDateSelect::class,
        'FormDateSelect'             => Helper\FormDateSelect::class,
        'form_element'               => Helper\FormElement::class,
        'formelement'                => Helper\FormElement::class,
        'formElement'                => Helper\FormElement::class,
        'FormElement'                => Helper\FormElement::class,
        'form_element_errors'        => Helper\FormElementErrors::class,
        'formelementerrors'          => Helper\FormElementErrors::class,
        'formElementErrors'          => Helper\FormElementErrors::class,
        'FormElementErrors'          => Helper\FormElementErrors::class,
        'form_email'                 => Helper\FormEmail::class,
        'formemail'                  => Helper\FormEmail::class,
        'formEmail'                  => Helper\FormEmail::class,
        'FormEmail'                  => Helper\FormEmail::class,
        'form_file'                  => Helper\FormFile::class,
        'formfile'                   => Helper\FormFile::class,
        'formFile'                   => Helper\FormFile::class,
        'FormFile'                   => Helper\FormFile::class,
        'formfileapcprogress'        => Helper\File\FormFileApcProgress::class,
        'form_file_apc_progress'     => Helper\File\FormFileApcProgress::class,
        'formFileApcProgress'        => Helper\File\FormFileApcProgress::class,
        'FormFileApcProgress'        => Helper\File\FormFileApcProgress::class,
        'formfilesessionprogress'    => Helper\File\FormFileSessionProgress::class,
        'form_file_session_progress' => Helper\File\FormFileSessionProgress::class,
        'formFileSessionProgress'    => Helper\File\FormFileSessionProgress::class,
        'FormFileSessionProgress'    => Helper\File\FormFileSessionProgress::class,
        'formfileuploadprogress'     => Helper\File\FormFileUploadProgress::class,
        'form_file_upload_progress'  => Helper\File\FormFileUploadProgress::class,
        'formFileUploadProgress'     => Helper\File\FormFileUploadProgress::class,
        'FormFileUploadProgress'     => Helper\File\FormFileUploadProgress::class,
        'formhidden'                 => Helper\FormHidden::class,
        'form_hidden'                => Helper\FormHidden::class,
        'formHidden'                 => Helper\FormHidden::class,
        'FormHidden'                 => Helper\FormHidden::class,
        'formimage'                  => Helper\FormImage::class,
        'form_image'                 => Helper\FormImage::class,
        'formImage'                  => Helper\FormImage::class,
        'FormImage'                  => Helper\FormImage::class,
        'forminput'                  => Helper\FormInput::class,
        'form_input'                 => Helper\FormInput::class,
        'formInput'                  => Helper\FormInput::class,
        'FormInput'                  => Helper\FormInput::class,
        'formlabel'                  => Helper\FormLabel::class,
        'form_label'                 => Helper\FormLabel::class,
        'formLabel'                  => Helper\FormLabel::class,
        'FormLabel'                  => Helper\FormLabel::class,
        'formmonth'                  => Helper\FormMonth::class,
        'form_month'                 => Helper\FormMonth::class,
        'formMonth'                  => Helper\FormMonth::class,
        'FormMonth'                  => Helper\FormMonth::class,
        'formmonthselect'            => Helper\FormMonthSelect::class,
        'form_month_select'          => Helper\FormMonthSelect::class,
        'formMonthSelect'            => Helper\FormMonthSelect::class,
        'FormMonthSelect'            => Helper\FormMonthSelect::class,
        'formmulticheckbox'          => Helper\FormMultiCheckbox::class,
        'form_multi_checkbox'        => Helper\FormMultiCheckbox::class,
        'formMultiCheckbox'          => Helper\FormMultiCheckbox::class,
        'FormMultiCheckbox'          => Helper\FormMultiCheckbox::class,
        'formnumber'                 => Helper\FormNumber::class,
        'form_number'                => Helper\FormNumber::class,
        'formNumber'                 => Helper\FormNumber::class,
        'FormNumber'                 => Helper\FormNumber::class,
        'formpassword'               => Helper\FormPassword::class,
        'form_password'              => Helper\FormPassword::class,
        'formPassword'               => Helper\FormPassword::class,
        'FormPassword'               => Helper\FormPassword::class,
        'formradio'                  => Helper\FormRadio::class,
        'form_radio'                 => Helper\FormRadio::class,
        'formRadio'                  => Helper\FormRadio::class,
        'FormRadio'                  => Helper\FormRadio::class,
        'formrange'                  => Helper\FormRange::class,
        'form_range'                 => Helper\FormRange::class,
        'formRange'                  => Helper\FormRange::class,
        'FormRange'                  => Helper\FormRange::class,
        'formreset'                  => Helper\FormReset::class,
        'form_reset'                 => Helper\FormReset::class,
        'formReset'                  => Helper\FormReset::class,
        'FormReset'                  => Helper\FormReset::class,
        'formrow'                    => Helper\FormRow::class,
        'form_row'                   => Helper\FormRow::class,
        'formRow'                    => Helper\FormRow::class,
        'FormRow'                    => Helper\FormRow::class,
        'formsearch'                 => Helper\FormSearch::class,
        'form_search'                => Helper\FormSearch::class,
        'formSearch'                 => Helper\FormSearch::class,
        'FormSearch'                 => Helper\FormSearch::class,
        'formselect'                 => Helper\FormSelect::class,
        'form_select'                => Helper\FormSelect::class,
        'formSelect'                 => Helper\FormSelect::class,
        'FormSelect'                 => Helper\FormSelect::class,
        'formsubmit'                 => Helper\FormSubmit::class,
        'form_submit'                => Helper\FormSubmit::class,
        'formSubmit'                 => Helper\FormSubmit::class,
        'FormSubmit'                 => Helper\FormSubmit::class,
        'formtel'                    => Helper\FormTel::class,
        'form_tel'                   => Helper\FormTel::class,
        'formTel'                    => Helper\FormTel::class,
        'FormTel'                    => Helper\FormTel::class,
        'formtext'                   => Helper\FormText::class,
        'form_text'                  => Helper\FormText::class,
        'formText'                   => Helper\FormText::class,
        'FormText'                   => Helper\FormText::class,
        'formtextarea'               => Helper\FormTextarea::class,
        'form_text_area'             => Helper\FormTextarea::class,
        'formTextArea'               => Helper\FormTextarea::class,
        'FormTextArea'               => Helper\FormTextarea::class,
        'formtime'                   => Helper\FormTime::class,
        'form_time'                  => Helper\FormTime::class,
        'formTime'                   => Helper\FormTime::class,
        'FormTime'                   => Helper\FormTime::class,
        'formurl'                    => Helper\FormUrl::class,
        'form_url'                   => Helper\FormUrl::class,
        'formUrl'                    => Helper\FormUrl::class,
        'FormUrl'                    => Helper\FormUrl::class,
        'formweek'                   => Helper\FormWeek::class,
        'form_week'                  => Helper\FormWeek::class,
        'formWeek'                   => Helper\FormWeek::class,
        'FormWeek'                   => Helper\FormWeek::class,
    ];

    protected $factories = [
        Helper\Form::class                          => InvokableFactory::class,
        Helper\FormButton::class                    => InvokableFactory::class,
        Helper\FormCaptcha::class                   => InvokableFactory::class,
        Helper\Captcha\Dumb::class                  => InvokableFactory::class,
        Helper\Captcha\Dumb::class                  => InvokableFactory::class,
        Helper\Captcha\Figlet::class                => InvokableFactory::class,
        Helper\Captcha\Figlet::class                => InvokableFactory::class,
        Helper\Captcha\Image::class                 => InvokableFactory::class,
        Helper\Captcha\Image::class                 => InvokableFactory::class,
        Helper\Captcha\ReCaptcha::class             => InvokableFactory::class,
        Helper\Captcha\ReCaptcha::class             => InvokableFactory::class,
        Helper\FormCheckbox::class                  => InvokableFactory::class,
        Helper\FormCollection::class                => InvokableFactory::class,
        Helper\FormColor::class                     => InvokableFactory::class,
        Helper\FormDate::class                      => InvokableFactory::class,
        Helper\FormDateTime::class                  => InvokableFactory::class,
        Helper\FormDateTimeLocal::class             => InvokableFactory::class,
        Helper\FormDateTimeSelect::class            => InvokableFactory::class,
        Helper\FormDateSelect::class                => InvokableFactory::class,
        Helper\FormElement::class                   => InvokableFactory::class,
        Helper\FormElementErrors::class             => InvokableFactory::class,
        Helper\FormEmail::class                     => InvokableFactory::class,
        Helper\FormFile::class                      => InvokableFactory::class,
        Helper\File\FormFileApcProgress::class      => InvokableFactory::class,
        Helper\File\FormFileSessionProgress::class  => InvokableFactory::class,
        Helper\File\FormFileUploadProgress::class   => InvokableFactory::class,
        Helper\FormHidden::class                    => InvokableFactory::class,
        Helper\FormImage::class                     => InvokableFactory::class,
        Helper\FormInput::class                     => InvokableFactory::class,
        Helper\FormLabel::class                     => InvokableFactory::class,
        Helper\FormMonth::class                     => InvokableFactory::class,
        Helper\FormMonthSelect::class               => InvokableFactory::class,
        Helper\FormMultiCheckbox::class             => InvokableFactory::class,
        Helper\FormNumber::class                    => InvokableFactory::class,
        Helper\FormPassword::class                  => InvokableFactory::class,
        Helper\FormRadio::class                     => InvokableFactory::class,
        Helper\FormRange::class                     => InvokableFactory::class,
        Helper\FormReset::class                     => InvokableFactory::class,
        Helper\FormRow::class                       => InvokableFactory::class,
        Helper\FormSearch::class                    => InvokableFactory::class,
        Helper\FormSelect::class                    => InvokableFactory::class,
        Helper\FormSubmit::class                    => InvokableFactory::class,
        Helper\FormTel::class                       => InvokableFactory::class,
        Helper\FormText::class                      => InvokableFactory::class,
        Helper\FormTextarea::class                  => InvokableFactory::class,
        Helper\FormTime::class                      => InvokableFactory::class,
        Helper\FormUrl::class                       => InvokableFactory::class,
        Helper\FormWeek::class                      => InvokableFactory::class,
    ];

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * Adds the invokables defined in this class to the SM managing helpers.
     *
     * @param ServiceManager $serviceManager
     * @return ServiceManager
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->factories as $service => $factory) {
            $serviceManager->setFactory($service, $factory);
        }

        foreach ($this->aliases as $alias => $target) {
            $serviceManager->setAlias($alias, $target);
        }

        return $serviceManager;
    }

    public function toArray()
    {
        return ['aliases' => $this->aliases, 'factories' => $this->factories];
    }
}
