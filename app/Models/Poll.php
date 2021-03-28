<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'polls';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'question' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function emailsList(): BelongsTo
    {
        return $this->belongsTo(EmailsList::class, 'emails_list_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'poll_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PollResult::class, 'poll_id');
    }
}
