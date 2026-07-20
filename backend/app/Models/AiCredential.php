<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class AiCredential extends Model
{
    protected $fillable = [
        'provider',
        'api_token',
        'token_hint',
        'default_model',
        'default_language',
        'default_article_length',
        'temperature',
        'max_articles_per_request',
        'is_active',
        'created_by',
        'updated_by',
        'token_updated_at',
        'last_tested_at',
        'last_test_status',
        'last_test_error',
    ];

    protected $hidden = [
        'api_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'temperature' => 'decimal:2',
        'max_articles_per_request' => 'integer',
        'default_article_length' => 'integer',
        'token_updated_at' => 'datetime',
        'last_tested_at' => 'datetime',
    ];

    /**
     * Explicitly sets and encrypts the api_token, extracting the token hint.
     */
    public function setApiToken(string $token): void
    {
        $this->attributes['api_token'] = Crypt::encryptString($token);
        $this->token_hint = substr($token, -4);
        $this->token_updated_at = now();
    }

    /**
     * Explicitly decrypts and returns the api_token.
     */
    public function getDecryptedToken(): ?string
    {
        $encrypted = $this->attributes['api_token'] ?? null;
        return $encrypted ? Crypt::decryptString($encrypted) : null;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
