<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Invitation
 *
 * @property int $id
 * @property string $token
 * @property string $email
 * @property int $poll_id
 * @property string|null $deleted_at
 * @property-read \App\Models\Poll $poll
 * @method static Builder|Invitation expired()
 * @method static \Database\Factories\InvitationFactory factory(...$parameters)
 * @method static Builder|Invitation newModelQuery()
 * @method static Builder|Invitation newQuery()
 * @method static Builder|Invitation notExpired()
 * @method static Builder|Invitation query()
 * @method static Builder|Invitation whereDeletedAt($value)
 * @method static Builder|Invitation whereEmail($value)
 * @method static Builder|Invitation whereId($value)
 * @method static Builder|Invitation wherePollId($value)
 * @method static Builder|Invitation whereToken($value)
 * @mixin \Eloquent
 */
class Invitation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitations';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Scope a query to only include expired invitations.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereRelation('poll', 'end', '<', now());
    }

    /**
     * Scope a query to only include not expired invitations.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->whereRelation('poll', 'end', '>', now());
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
