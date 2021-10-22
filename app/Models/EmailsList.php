<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\EmailsList
 *
 * @property int $id
 * @property int $owner_id
 * @property string $title
 * @property array|null $emails
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\Poll|null $poll
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList whereEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailsList whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Poll[] $polls
 * @property-read int|null $polls_count
 * @method static \Database\Factories\EmailsListFactory factory(...$parameters)
 */
class EmailsList extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emails_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['title', 'emails'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'emails' => 'array',
    ];

    /**
     * Poll relation
     *
     * @return HasMany
     */
    public function polls(): HasMany
    {
        return $this->HasMany(Poll::class, 'emails_list_id');
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
}
