<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Poll
 *
 * @property int $id
 * @property int $owner_id
 * @property int|null $emails_list_id
 * @property string $title
 * @property string|null $description
 * @property string|null $short_description
 * @property array|null $question
 * @property \Illuminate\Support\Carbon|null $start
 * @property \Illuminate\Support\Carbon|null $end
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EmailsList|null $emailsList
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invitation[] $invitations
 * @property-read int|null $invitations_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PollResult[] $results
 * @property-read int|null $results_count
 * @method static Builder|Poll draft()
 * @method static Builder|Poll newModelQuery()
 * @method static Builder|Poll newQuery()
 * @method static Builder|Poll published()
 * @method static Builder|Poll query()
 * @method static Builder|Poll whereCreatedAt($value)
 * @method static Builder|Poll whereDescription($value)
 * @method static Builder|Poll whereEmailsListId($value)
 * @method static Builder|Poll whereEnd($value)
 * @method static Builder|Poll whereId($value)
 * @method static Builder|Poll whereOwnerId($value)
 * @method static Builder|Poll wherePublishedAt($value)
 * @method static Builder|Poll whereQuestion($value)
 * @method static Builder|Poll whereShortDescription($value)
 * @method static Builder|Poll whereStart($value)
 * @method static Builder|Poll whereTitle($value)
 * @method static Builder|Poll whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\PollFactory factory(...$parameters)
 * @method static \Illuminate\Database\Query\Builder|Poll onlyTrashed()
 * @method static Builder|Poll whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Poll withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Poll withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Voter[] $voters
 * @property-read int|null $voters_count
 */
class Poll extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'polls';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'description',
        'short_description',
        'start',
        'end',
        'question',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'question' => 'array',
        'start' => 'datetime',
        'end' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Determine if poll is currently taking part in voting
     *
     * @return bool
     */
    public function isInVoting(): bool
    {
        return $this->published_at && $this->start->isBefore(now()) && $this->end->isAfter(now());
    }

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

    /**
     * Owner(User) relation
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * EmailsList(s) relation
     *
     * @return BelongsTo
     */
    public function emailsList(): BelongsTo
    {
        return $this->belongsTo(EmailsList::class, 'emails_list_id');
    }

    /**
     * Invitation relation
     *
     * @return HasMany
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'poll_id');
    }

    /**
     * Poll results relation
     *
     * @return HasMany
     */
    public function results(): HasMany
    {
        return $this->hasMany(PollResult::class, 'poll_id');
    }

    /**
     * Poll voters relation
     *
     * @return HasMany
     */
    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class, 'poll_id');
    }
}
