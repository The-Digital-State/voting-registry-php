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

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'token';

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }
}
