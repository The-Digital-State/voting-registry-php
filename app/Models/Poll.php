<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CLOSED = 'closed';

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'polls';

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'question',
        'emails_list_id',
        'creator_id'
    ];

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

    public function status(): string
    {
        if ($this->published_at !== null) {
            if ($this->started_at <= new \DateTime() && $this->ended_at >= new \DateTime()) {
                return self::STATUS_ACTIVE;
            }

            if ($this->ended_at <= new \DateTime()) {
                return self::STATUS_FINISHED;
            }
        }

        return self::STATUS_DRAFT;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
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
