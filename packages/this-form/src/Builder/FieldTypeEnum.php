<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\Form\Builder;

enum FieldTypeEnum: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case PASSWORD = 'password';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO_GROUP = 'radio_group';
    case CHECKBOX_GROUP = 'checkbox_group';
    case BUTTON = 'button';
    case FILE = 'file';
    case HIDDEN = 'hidden';
}
