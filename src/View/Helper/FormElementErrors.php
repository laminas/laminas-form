<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

use function array_merge;
use function array_walk_recursive;
use function count;
use function implode;
use function sprintf;

class FormElementErrors extends AbstractHelper
{
    /** @var string Templates for the open/close/separators for message tags */
    protected $messageOpenFormat = '<ul%s><li>';
    /** @var string Templates for the open/close/separators for message tags */
    protected $messageCloseString = '</li></ul>';
    /** @var string Templates for the open/close/separators for message tags */
    protected $messageSeparatorString = '</li><li>';

    /** @var array Default attributes for the open format tag */
    protected $attributes = [];

    /** @var bool Whether or not to translate error messages during render. */
    protected $translateErrorMessages = true;

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()} if an element is passed.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @param  array $attributes
     * @return string|FormElementErrors
     */
    public function __invoke(?ElementInterface $element = null, array $attributes = [])
    {
        if (! $element) {
            return $this;
        }

        return $this->render($element, $attributes);
    }

    /**
     * Render validation errors for the provided $element
     *
     * If {@link $translateErrorMessages} is true, and a translator is
     * composed, messages retrieved from the element will be translated; if
     * either is not the case, they will not.
     *
     * @param  array $attributes
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element, array $attributes = []): string
    {
        $messages = $element->getMessages();
        if (! $messages) {
            return '';
        }

        // Flatten message array
        $messages = $this->flattenMessages($messages);
        if (! $messages) {
            return '';
        }

        // Prepare attributes for opening tag
        $attributes = array_merge($this->attributes, $attributes);
        $attributes = $this->createAttributesString($attributes);
        if (! empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        $count   = count($messages);
        $escaper = $this->getEscapeHtmlHelper();
        for ($i = 0; $i < $count; $i += 1) {
            $messages[$i] = $escaper($messages[$i]);
        }

        // Generate markup
        $markup  = sprintf($this->getMessageOpenFormat(), $attributes);
        $markup .= implode($this->getMessageSeparatorString(), $messages);
        $markup .= $this->getMessageCloseString();

        return $markup;
    }

    /**
     * Set the attributes that will go on the message open format
     *
     * @param  array $attributes key value pairs of attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Get the attributes that will go on the message open format
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the string used to close message representation
     *
     * @return $this
     */
    public function setMessageCloseString(string $messageCloseString)
    {
        $this->messageCloseString = $messageCloseString;
        return $this;
    }

    /**
     * Get the string used to close message representation
     */
    public function getMessageCloseString(): string
    {
        return $this->messageCloseString;
    }

    /**
     * Set the formatted string used to open message representation
     *
     * @return $this
     */
    public function setMessageOpenFormat(string $messageOpenFormat)
    {
        $this->messageOpenFormat = $messageOpenFormat;
        return $this;
    }

    /**
     * Get the formatted string used to open message representation
     */
    public function getMessageOpenFormat(): string
    {
        return $this->messageOpenFormat;
    }

    /**
     * Set the string used to separate messages
     *
     * @return $this
     */
    public function setMessageSeparatorString(string $messageSeparatorString)
    {
        $this->messageSeparatorString = $messageSeparatorString;
        return $this;
    }

    /**
     * Get the string used to separate messages
     */
    public function getMessageSeparatorString(): string
    {
        return $this->messageSeparatorString;
    }

    /**
     * Set the flag detailing whether or not to translate error messages.
     *
     * @return $this
     */
    public function setTranslateMessages(bool $flag)
    {
        $this->translateErrorMessages = $flag;
        return $this;
    }

    /**
     * @return array
     */
    private function flattenMessages(array $messages): array
    {
        return $this->translateErrorMessages && $this->getTranslator()
            ? $this->flattenMessagesWithTranslator($messages)
            : $this->flattenMessagesWithoutTranslator($messages);
    }

    /**
     * @return array
     */
    private function flattenMessagesWithoutTranslator(array $messages): array
    {
        $messagesToPrint = [];
        array_walk_recursive($messages, static function (string $item) use (&$messagesToPrint): void {
            $messagesToPrint[] = $item;
        });
        return $messagesToPrint;
    }

    /**
     * @return array
     */
    private function flattenMessagesWithTranslator(array $messages): array
    {
        $translator      = $this->getTranslator();
        $textDomain      = $this->getTranslatorTextDomain();
        $messagesToPrint = [];
        $messageCallback = static function ($item) use (&$messagesToPrint, $translator, $textDomain): void {
            $messagesToPrint[] = $translator->translate($item, $textDomain);
        };
        array_walk_recursive($messages, $messageCallback);
        return $messagesToPrint;
    }
}
