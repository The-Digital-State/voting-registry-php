<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PollResult
 *
 * @property int $id
 * @property string $token
 * @property int $poll_id
 * @property string $choice
 * @property-read \App\Models\Poll $poll
 * @method static \Illuminate\Database\Eloquent\Builder|PollResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|PollResult whereChoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollResult wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollResult whereToken($value)
 * @mixin \Eloquent
 * @method static Builder|PollResult published()
 */
class PollResult extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'polls_results';

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
     * Poll relation
     *
     * @return BelongsTo
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }
}
