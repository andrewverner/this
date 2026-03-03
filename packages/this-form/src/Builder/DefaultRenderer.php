<?php

declare(strict_types=1);

namespace This\Form\Builder;

final class DefaultRenderer
{
    public static function render(Field $field): string
    {
        $label = self::renderLabel($field) ?? '';

        $input = match ($field->getType()) {
            FieldTypeEnum::PASSWORD => self::voidInput($field, 'password'),
            FieldTypeEnum::FILE => self::file($field),
            FieldTypeEnum::TEXTAREA => self::textArea($field),
            FieldTypeEnum::SELECT => self::select($field),
            FieldTypeEnum::RADIO_GROUP => self::radioGroup($field),
            FieldTypeEnum::CHECKBOX => self::checkbox($field),
            FieldTypeEnum::CHECKBOX_GROUP => self::checkboxGroup($field),
            FieldTypeEnum::BUTTON => self::button($field),
            FieldTypeEnum::HIDDEN => self::hidden($field),
            default => self::voidInput($field, 'text'),
        };

        return in_array($field->getType(), [FieldTypeEnum::CHECKBOX]) ? $input . $label : $label . $input;
    }

    private static function voidInput(Field $field, string $type): string
    {
        return implode(' ', array_filter([
            '<input',
            'type="' . $type . '"',
            'name="' . self::escape($field->getName()) . '"',
            'value="' . self::escape((string)$field->getValue()) . '"',
            $field->isRequired() ? 'required' : null,
            $field->getMeta('disabled') ? 'disabled' : null,
            self::renderAttributes($field),
            $field->getMeta('readonly') ? 'readonly' : null,
            $field->getMeta('placeholder') ? 'placeholder="' . self::escape($field->getMeta('placeholder')) . '"' : null,
            '/>',
        ]));
    }

    private static function hidden(Field $field): string
    {
        return implode(' ', array_filter([
            '<input',
            'type="hidden"',
            'name="' . self::escape($field->getName()) . '"',
            'value="' . self::escape((string)$field->getValue()) . '"',
            '/>',
        ]));
    }

    private static function file(Field $field): string
    {
        $multiple = $field->getMeta('multiple') === true;

        return implode(' ', array_filter([
            '<input',
            'type="file"',
            'name="' . self::escape(self::resolveName($field, $multiple)) . '"',
            $multiple ? 'multiple' : null,
            $field->isRequired() ? 'required' : null,
            $field->getMeta('disabled') ? 'disabled' : null,
            self::renderAttributes($field),
            '/>',
        ]));
    }

    private static function textArea(Field $field): string
    {
        return implode(' ', array_filter([
            '<textarea',
            'name="' . self::escape($field->getName()) . '"',
            $field->isRequired() ? 'required' : null,
            $field->getMeta('disabled') ? 'disabled' : null,
            self::renderAttributes($field),
            $field->getMeta('readonly') ? 'readonly' : null,
            $field->getMeta('placeholder') ? 'placeholder="' . self::escape($field->getMeta('placeholder')) . '"' : null,
            '>' . self::escape((string)$field->getValue()) . '</textarea>',
        ]));
    }

    private static function checkbox(Field $field): string
    {
        return implode(' ', array_filter([
            '<input',
            'type="checkbox"',
            'name="' . self::escape($field->getName()) . '"',
            'value="1"',
            $field->getValue() ? 'checked' : null,
            $field->isRequired() ? 'required' : null,
            $field->getMeta('disabled') ? 'disabled' : null,
            self::renderAttributes($field),
            '/> ',
        ]));
    }

    private static function select(Field $field): string
    {
        $multiple = $field->getMeta('multiple') === true;
        $selectedValues = $multiple
            ? (array)$field->getValue()
            : [$field->getValue()];

        $options = '';

        foreach ($field->getData() as $value => $label) {
            $options .= implode(' ', array_filter([
                '<option',
                'value="' . self::escape((string)$value) . '"',
                in_array($value, $selectedValues, true) ? 'selected' : null,
                '>' . self::escape((string)$label) . '</option>',
            ]));
        }

        return implode(' ', array_filter([
            '<select',
            'name="' . self::escape(self::resolveName($field, $multiple)) . '"',
            $multiple ? 'multiple' : null,
            $field->isRequired() ? 'required' : null,
            $field->getMeta('disabled') ? 'disabled' : null,
            self::renderAttributes($field),
            '>',
            $options,
            '</select>',
        ]));
    }

    private static function radioGroup(Field $field): string
    {
        $current = $field->getValue();
        $list = [];

        foreach ($field->getData() as $value => $label) {
            $list[] = implode(' ', array_filter([
                '<label>',
                '<input',
                'type="radio"',
                'name="' . self::escape($field->getName()) . '"',
                'value="' . self::escape((string)$value) . '"',
                $current === $value ? 'checked' : null,
                $field->isRequired() ? 'required' : null,
                $field->getMeta('disabled') ? 'disabled' : null,
                self::renderAttributes($field),
                '/>',
                self::escape((string)$label) . '</label>',
            ]));
        }

        return implode('', $list);
    }

    private static function checkboxGroup(Field $field): string
    {
        $selectedValues = (array)$field->getValue();
        $list = [];

        foreach ($field->getData() as $value => $label) {
            $list[] = implode(' ', array_filter([
                '<label>',
                '<input',
                'type="checkbox"',
                'name="' . self::escape($field->getName()) . '[]"',
                'value="' . self::escape((string)$value) . '"',
                in_array($value, $selectedValues, true) ? 'checked' : null,
                $field->isRequired() ? 'required' : null,
                $field->getMeta('disabled') ? 'disabled' : null,
                self::renderAttributes($field),
                '/>',
                self::escape((string)$label) . '</label>',
            ]));
        }

        return implode('', $list);
    }

    private static function button(Field $field): string
    {
        $type = match ($field->getMeta('type')) {
            'submit' => 'submit',
            'reset' => 'reset',
            default => 'button',
        };

        return implode(' ', array_filter([
            '<input',
            'type="' . $type . '"',
            'value="' . self::escape((string)$field->getValue()) . '"',
            $field->getMeta('disabled') ? 'disabled' : null,
            self::renderAttributes($field),
            '/>',
        ]));
    }

    private static function renderLabel(Field $field): ?string
    {
        if (!$field->getLabel()) {
            return null;
        }

        $class = self::mergeClasses(
            null,
            $field->isRequired() ? $field->getRequiredClass() : null,
            $field->isInvalid() ? $field->getErrorClass() : null,
        );

        return implode(' ', array_filter([
            '<label',
            $class ? 'class="' . self::escape($class) . '"' : null,
            '>' . self::escape($field->getLabel()) . '</label>',
        ]));
    }

    private static function renderAttributes(Field $field): ?string
    {
        $attributes = $field->getAttributes();

        $attributes['class'] = self::mergeClasses(
            $attributes['class'] ?? null,
            $field->isRequired() ? $field->getRequiredClass() : null,
            $field->isInvalid() ? $field->getErrorClass() : null,
        );

        if (!$attributes) {
            return null;
        }

        $result = [];

        foreach ($attributes as $name => $value) {
            if ($value === null) {
                continue;
            }

            $result[] = sprintf(
                '%s="%s"',
                self::escape((string)$name),
                self::escape((string)$value)
            );
        }

        return implode(' ', $result);
    }

    private static function mergeClasses(?string $existing, ?string ...$classes): ?string
    {
        $all = array_filter(array_merge(
            $existing ? explode(' ', $existing) : [],
            array_filter($classes)
        ));

        $all = array_unique($all);

        return $all ? implode(' ', $all) : null;
    }

    private static function resolveName(Field $field, bool $multiple): string
    {
        return $multiple ? $field->getName() . '[]' : $field->getName();
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
