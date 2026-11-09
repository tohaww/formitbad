<?php

namespace App\Models;

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'form_id',
        'label',
        'field_type',
        'order_index',
        'is_required',
        'placeholder',
        'config',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'field_type' => FieldType::class,
        'order_index' => 'integer',
        'is_required' => 'boolean',
        'config' => 'array',
    ];

    /**
     * The parent form.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}

