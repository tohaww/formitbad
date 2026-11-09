<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar_url',
        'google_sheets_access_token',
        'google_sheets_refresh_token',
        'google_sheets_token_expires_at',
        'google_sheets_email',
        'google_sheets_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_sheets_access_token',
        'google_sheets_refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google_sheets_token_expires_at' => 'datetime',
        ];
    }

    /**
     * Check if user has connected their Google Sheets account via OAuth.
     */
    public function hasGoogleSheetsConnected(): bool
    {
        return !empty($this->google_sheets_refresh_token) || !empty($this->google_sheets_access_token);
    }

    /**
     * Forms owned by this user.
     */
    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
    }

    /**
     * Submissions across all forms of this user.
     */
    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(Submission::class, Form::class);
    }
}
