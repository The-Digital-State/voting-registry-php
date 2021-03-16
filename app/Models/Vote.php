<?php


namespace App\Models;


use Atk4\Core;
use Atk4\Data\Persistence;
use App\Models;

class Vote extends \Atk4\Data\Model
{
    public $table = 'vote';
    public $title_field = 'token';
    public function __construct(Persistence $persistence = null)
    {
        parent::__construct($persistence);
    }

    protected function init(): void
    {
        parent::init();

        $this->hasOne('poll_id', ['model'=>new Models\Poll()]);//->withTitle();
        $this->hasOne('choice_id', ['model'=>new Models\Choice()]);//->withTitle();

        $this->addField('token');
        $this->addField('ts', ['type'=>'datetime']);
    }
}
