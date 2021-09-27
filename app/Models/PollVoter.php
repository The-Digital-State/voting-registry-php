<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PollVoter
 *
 * @property int $id
 * @property int $voter_id
 * @property int $poll_id
 * @property string $voted_at
 * @property-read \App\Models\Poll $poll
 * @property-read \App\Models\User $voter
 * @method static \Illuminate\Database\Eloquent\Builder|PollVoter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollVoter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollVoter query()
 * @method static \Illuminate\Database\Eloquent\Builder|PollVoter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollVoter wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollVoter whereVotedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollVoter whereVoterId($value)
 * @mixin \Eloquent
 * @method static Builder|PollVoter voted()
 */
class PollVoter extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'polls_voters';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Voter(User) relation
     *
     * @return BelongsTo
     */
    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    /**
     * Scope a query to only include voted voters.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeVoted(Builder $query): Builder
    {
        return $query->whereNotNull('voted_at');
    }

    /**
     * Poll relation
     *
     * @return BelongsTo
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }
}
