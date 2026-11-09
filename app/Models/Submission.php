<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'form_id',
        'payload',
        'sheet_synced',
        'webhook_delivered',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'sheet_synced' => 'boolean',
        'webhook_delivered' => 'boolean',
    ];

    /**
     * The parent form.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}

