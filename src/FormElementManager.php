<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\Form\Exception;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\Stdlib\InitializableInterface;
use Psr\Container\ContainerInterface;

use function array_push;
use function array_search;
use function array_unshift;
use function class_exists;
use function gettype;
use function is_object;
use function sprintf;

/**
 * laminas-servicemanager v3-compatible plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 *
 * @final
 * @extends AbstractPluginManager<ElementInterface>
 */
class FormElementManager extends AbstractPluginManager
{
    /**
     * Aliases for default set of helpers
     *
     * @var array
     */
    protected $aliases = [
        'button'         => Element\Button::class,
        'Button'         => Element\Button::class,
        'captcha'        => Element\Captcha::class,
        'Captcha'        => Element\Captcha::class,
        'checkbox'       => Element\Checkbox::class,
        'Checkbox'       => Element\Checkbox::class,
        'collection'     => Element\Collection::class,
        'Collection'     => Element\Collection::class,
        'color'          => Element\Color::class,
        'Color'          => Element\Color::class,
        'csrf'           => Element\Csrf::class,
        'Csrf'           => Element\Csrf::class,
        'date'           => Element\Date::class,
        'Date'           => Element\Date::class,
        'dateselect'     => Element\DateSelect::class,
        'dateSelect'     => Element\DateSelect::class,
        'DateSelect'     => Element\DateSelect::class,
        'datetime'       => Element\DateTime::class,
        'dateTime'       => Element\DateTime::class,
        'DateTime'       => Element\DateTime::class,
        'datetimelocal'  => Element\DateTimeLocal::class,
        'dateTimeLocal'  => Element\DateTimeLocal::class,
        'DateTimeLocal'  => Element\DateTimeLocal::class,
        'datetimeselect' => Element\DateTimeSelect::class,
        'dateTimeSelect' => Element\DateTimeSelect::class,
        'DateTimeSelect' => Element\DateTimeSelect::class,
        'element'        => Element::class,
        'Element'        => Element::class,
        'email'          => Element\Email::class,
        'Email'          => Element\Email::class,
        'fieldset'       => Fieldset::class,
        'Fieldset'       => Fieldset::class,
        'file'           => Element\File::class,
        'File'           => Element\File::class,
        'form'           => Form::class,
        'Form'           => Form::class,
        'hidden'         => Element\Hidden::class,
        'Hidden'         => Element\Hidden::class,
        'image'          => Element\Image::class,
        'Image'          => Element\Image::class,
        'month'          => Element\Month::class,
        'Month'          => Element\Month::class,
        'monthselect'    => Element\MonthSelect::class,
        'monthSelect'    => Element\MonthSelect::class,
        'MonthSelect'    => Element\MonthSelect::class,
        'multicheckbox'  => Element\MultiCheckbox::class,
        'multiCheckbox'  => Element\MultiCheckbox::class,
        'MultiCheckbox'  => Element\MultiCheckbox::class,
        'multiCheckBox'  => Element\MultiCheckbox::class,
        'MultiCheckBox'  => Element\MultiCheckbox::class,
        'number'         => Element\Number::class,
        'Number'         => Element\Number::class,
        'password'       => Element\Password::class,
        'Password'       => Element\Password::class,
        'radio'          => Element\Radio::class,
        'Radio'          => Element\Radio::class,
        'range'          => Element\Range::class,
        'Range'          => Element\Range::class,
        'search'         => Element\Search::class,
        'Search'         => Element\Search::class,
        'select'         => Element\Select::class,
        'Select'         => Element\Select::class,
        'submit'         => Element\Submit::class,
        'Submit'         => Element\Submit::class,
        'tel'            => Element\Tel::class,
        'Tel'            => Element\Tel::class,
        'text'           => Element\Text::class,
        'Text'           => Element\Text::class,
        'textarea'       => Element\Textarea::class,
        'Textarea'       => Element\Textarea::class,
        'time'           => Element\Time::class,
        'Time'           => Element\Time::class,
        'url'            => Element\Url::class,
        'Url'            => Element\Url::class,
        'week'           => Element\Week::class,
        'Week'           => Element\Week::class,
    ];

    /**
     * Factories for default set of helpers
     *
     * @var array
     */
    protected $factories = [
        Element\Button::class         => ElementFactory::class,
        Element\Captcha::class        => ElementFactory::class,
        Element\Checkbox::class       => ElementFactory::class,
        Element\Collection::class     => ElementFactory::class,
        Element\Color::class          => ElementFactory::class,
        Element\Csrf::class           => ElementFactory::class,
        Element\Date::class           => ElementFactory::class,
        Element\DateSelect::class     => ElementFactory::class,
        Element\DateTime::class       => ElementFactory::class,
        Element\DateTimeLocal::class  => ElementFactory::class,
        Element\DateTimeSelect::class => ElementFactory::class,
        Element::class                => ElementFactory::class,
        Element\Email::class          => ElementFactory::class,
        Fieldset::class               => ElementFactory::class,
        Element\File::class           => ElementFactory::class,
        Form::class                   => ElementFactory::class,
        Element\Hidden::class         => ElementFactory::class,
        Element\Image::class          => ElementFactory::class,
        Element\Month::class          => ElementFactory::class,
        Element\MonthSelect::class    => ElementFactory::class,
        Element\MultiCheckbox::class  => ElementFactory::class,
        Element\Number::class         => ElementFactory::class,
        Element\Password::class       => ElementFactory::class,
        Element\Radio::class          => ElementFactory::class,
        Element\Range::class          => ElementFactory::class,
        Element\Search::class         => ElementFactory::class,
        Element\Select::class         => ElementFactory::class,
        Element\Submit::class         => ElementFactory::class,
        Element\Tel::class            => ElementFactory::class,
        Element\Text::class           => ElementFactory::class,
        Element\Textarea::class       => ElementFactory::class,
        Element\Time::class           => ElementFactory::class,
        Element\Url::class            => ElementFactory::class,
        Element\Week::class           => ElementFactory::class,

        // v2 normalized variants
        'laminasformelementbutton'         => ElementFactory::class,
        'laminasformelementcaptcha'        => ElementFactory::class,
        'laminasformelementcheckbox'       => ElementFactory::class,
        'laminasformelementcollection'     => ElementFactory::class,
        'laminasformelementcolor'          => ElementFactory::class,
        'laminasformelementcsrf'           => ElementFactory::class,
        'laminasformelementdate'           => ElementFactory::class,
        'laminasformelementdateselect'     => ElementFactory::class,
        'laminasformelementdatetime'       => ElementFactory::class,
        'laminasformelementdatetimelocal'  => ElementFactory::class,
        'laminasformelementdatetimeselect' => ElementFactory::class,
        'laminasformelement'               => ElementFactory::class,
        'laminasformelementemail'          => ElementFactory::class,
        'laminasformfieldset'              => ElementFactory::class,
        'laminasformelementfile'           => ElementFactory::class,
        'laminasformform'                  => ElementFactory::class,
        'laminasformelementhidden'         => ElementFactory::class,
        'laminasformelementimage'          => ElementFactory::class,
        'laminasformelementmonth'          => ElementFactory::class,
        'laminasformelementmonthselect'    => ElementFactory::class,
        'laminasformelementmulticheckbox'  => ElementFactory::class,
        'laminasformelementnumber'         => ElementFactory::class,
        'laminasformelementpassword'       => ElementFactory::class,
        'laminasformelementradio'          => ElementFactory::class,
        'laminasformelementrange'          => ElementFactory::class,
        'laminasformelementsearch'         => ElementFactory::class,
        'laminasformelementselect'         => ElementFactory::class,
        'laminasformelementsubmit'         => ElementFactory::class,
        'laminasformelementtel'            => ElementFactory::class,
        'laminasformelementtext'           => ElementFactory::class,
        'laminasformelementtextarea'       => ElementFactory::class,
        'laminasformelementtime'           => ElementFactory::class,
        'laminasformelementurl'            => ElementFactory::class,
        'laminasformelementweek'           => ElementFactory::class,
    ];

    /**
     * Don't share form elements by default (v3)
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * Interface all plugins managed by this class must implement.
     *
     * @var class-string
     */
    protected $instanceOf = ElementInterface::class;

    /**
     * Inject the factory to any element that implements FormFactoryAwareInterface
     *
     * @param mixed $instance Instance to inspect and optionally inject.
     */
    public function injectFactory(ContainerInterface $container, mixed $instance): void
    {
        if (! $instance instanceof Fieldset) {
            return;
        }

        $factory = $instance->getFormFactory();
        $factory->setFormElementManager($this);

        if ($container->has(InputFilterPluginManager::class)) {
            $inputFilters = $container->get(InputFilterPluginManager::class);
            $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);
        }
    }

    /**
     * Call init() on any element that implements InitializableInterface
     *
     * @param mixed $instance Instance to inspect and optionally initialize.
     */
    public function callElementInit(ContainerInterface $container, mixed $instance): void
    {
        if ($instance instanceof InitializableInterface) {
            $instance->init();
        }
    }

    /**
     * Override setInvokableClass
     *
     * Overrides setInvokableClass to:
     *
     * - add a factory mapping $invokableClass to ElementFactory::class
     * - alias $name to $invokableClass
     *
     * @param string $name
     * @param null|string $class
     */
    public function setInvokableClass($name, $class = null): void
    {
        $class = $class ?: $name;

        if (! $this->has($class)) {
            $this->setFactory($class, ElementFactory::class);
        }

        if ($class === $name) {
            return;
        }

        $this->setAlias($name, $class);
    }

    /**
     * Validate the plugin is of the expected type (v3).
     *
     * Validates against `$instanceOf`.
     *
     * @param  mixed $instance
     * @throws InvalidServiceException
     * @psalm-assert ElementInterface $instance
     */
    public function validate($instance): void
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                static::class,
                $this->instanceOf,
                is_object($instance) ? $instance::class : gettype($instance)
            ));
        }
    }

    /**
     * Overrides parent::configure in order to ensure default initializers are in expected positions.
     *
     * Always pushes `injectFactory` to top of initializer stack, and
     * `callElementInit` to the bottom.
     *
     * @inheritDoc
     */
    public function configure(array $config)
    {
        $firstInitializer = [$this, 'injectFactory'];
        $lastInitializer  = [$this, 'callElementInit'];

        foreach ([$firstInitializer, $lastInitializer] as $default) {
            if (false === ($index = array_search($default, $this->initializers))) {
                continue;
            }
            unset($this->initializers[$index]);
        }

        parent::configure($config);

        array_unshift($this->initializers, $firstInitializer);
        array_push($this->initializers, $lastInitializer);

        return $this;
    }

    /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param class-string<ElementInterface>|string $name Service name of plugin to retrieve.
     * @param null|array<mixed> $options Options to use when creating the instance.
     * @psalm-return ($name is class-string<ElementInterface> ? ElementInterface : mixed)
     */
    public function get($name, ?array $options = null): mixed
    {
        if (! $this->has($name)) {
            if (! $this->autoAddInvokableClass || ! class_exists($name)) {
                throw new Exception\InvalidElementException(
                    sprintf(
                        'A plugin by the name "%s" was not found in the plugin manager %s',
                        $name,
                        static::class
                    )
                );
            }

            $this->setInvokableClass($name, $name);
        }
        return parent::get($name, $options);
    }

    /**
     * Try to pull hydrator from the creation context, or instantiates it from its name
     *
     * @param string|class-string<HydratorInterface> $hydratorName
     * @return mixed
     * @psalm-return ($hydratorName is class-string<HydratorInterface> ? HydratorInterface : mixed)
     * @throws Exception\DomainException
     */
    public function getHydratorFromName(string $hydratorName)
    {
        $services = $this->creationContext;

        if ($services && $services->has(HydratorPluginManager::class)) {
            $hydrators = $services->get(HydratorPluginManager::class);
            if ($hydrators->has($hydratorName)) {
                return $hydrators->get($hydratorName);
            }
        }

        if ($services && $services->has($hydratorName)) {
            return $services->get($hydratorName);
        }

        if (! class_exists($hydratorName)) {
            throw new Exception\DomainException(sprintf(
                'Expects string hydrator name to be a valid class name; received "%s"',
                $hydratorName
            ));
        }

        return new $hydratorName();
    }

    /**
     * Try to pull factory from the creation context, or instantiates it from its name
     *
     * @return mixed
     * @throws Exception\DomainException
     */
    public function getFactoryFromName(string $factoryName)
    {
        $services = $this->creationContext;

        if ($services && $services->has($factoryName)) {
            return $services->get($factoryName);
        }

        if (! class_exists($factoryName)) {
            throw new Exception\DomainException(sprintf(
                'Expects string factory name to be a valid class name; received "%s"',
                $factoryName
            ));
        }

        return new $factoryName();
    }
}
