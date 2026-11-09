<?php

namespace App\Enums;

enum FieldType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case SELECT = 'select';
    case RADIO = 'radio';
    case CHECKBOX = 'checkbox';
    case DATE = 'date';
    case TIME = 'time';
    case FILE = 'file';
    case URL = 'url';
    case RATING = 'rating';
    case REMOTE_DATA_DROPDOWN = 'remote_data_dropdown';

    /**
     * Get all available values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

