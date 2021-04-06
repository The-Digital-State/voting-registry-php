<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollResult extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'polls_results';

    public $timestamps = false;

    protected $fillable = [
        'token',
        'poll_id',
        'choice',
        'choice_index',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }
}
