<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmailsList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emails_lists';

    public function poll(): HasOne
    {
        return $this->hasOne(Poll::class, 'emails_list_id');
    }
}
