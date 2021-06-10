<?php

namespace App\Models;

use App\Observers\PollObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Poll
 * @property string $title
 * @property string $description
 * @property string $short_description
 * @property Carbon|null $started_at
 * @property Carbon|null $ended_at
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property array $question
 * @property integer $emails_list_id
 * @property integer $creator_id
 * @property-read User $creator
 * @property-read EmailsList $emailsList
 * @property-read Invitation[] $invitations
 * @property-read PollResult[] $results
 * @package App\Models
 */
class Poll extends Model
{
    use HasFactory;
    use PollObserver;

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
        'started_at',
        'ended_at',
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

    /**
     * Scope a query to only include draft polls.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->whereNull('published_at');
    }

    /**
     * Scope a query to only include published polls.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
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
