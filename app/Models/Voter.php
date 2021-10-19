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
 * @property int $user_id
 * @property int $poll_id
 * @property string $voted_at
 * @property-read \App\Models\Poll $poll
 * @property-read \App\Models\User $voter
 * @method static \Illuminate\Database\Eloquent\Builder|Voter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereVotedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereUserId($value)
 * @mixin \Eloquent
 * @method static Builder|Voter voted()
 * @property-read \App\Models\User $user
 */
class Voter extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'voters';

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
