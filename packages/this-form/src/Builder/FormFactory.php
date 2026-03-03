<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\Form\Builder;

final class FormFactory
{
    /**
     * @var array<FieldRendererInterface>
     */
    private static array $fieldRenderers = [];

    /**
     * @var array<FormRendererInterface>
     */
    private static array $formRenderers = [];

    private static string $requiredClass = 'required';

    private static string $errorClass = 'error';

    public static function setFieldRenderers(array $renderers): void
    {
        self::$fieldRenderers = $renderers;
    }

    public static function setFormRenderers(array $renderers): void
    {
        self::$formRenderers = $renderers;
    }

    public static function setRequiredClass(string $class): void
    {
        self::$requiredClass = $class;
    }

    public static function setErrorClass(string $class): void
    {
        self::$errorClass = $class;
    }

    public static function form(?object $object = null): Form
    {
        return new Form($object, self::$requiredClass, self::$errorClass, self::$fieldRenderers, self::$formRenderers);
    }
}
