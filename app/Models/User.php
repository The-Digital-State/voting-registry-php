<?php

namespace App\Models;

use Atk4\Data\Model;
use Atk4\Data\Persistence;

class User extends Model {
    public $table = 'user';
    public function __construct(Persistence $persistence = null)
    {
        parent::__construct($persistence);
    }

    protected function init(): void
    {
        parent::init();

        $this->addField('name');
        $this->addField('email');

        $this->addField('microsoft_teams_id');

        $this->addField('meta_document_id');
    }
}

//class User extends Model implements AuthenticatableContract, AuthorizableContract
//{
//    use Authenticatable, Authorizable, HasFactory;
//
//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var array
//     */
//    protected $fillable = [
//        'name', 'email',
//    ];
//
//    /**
//     * The attributes excluded from the model's JSON form.
//     *
//     * @var array
//     */
//    protected $hidden = [
//        'password',
//    ];
//}
