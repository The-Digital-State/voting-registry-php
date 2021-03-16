<?php


namespace App\Models;


use Atk4\Data\Persistence;

class EmailList extends \Atk4\Data\Model
{
    public $table = 'email_list';
    public function __construct(Persistence $persistence = null)
    {
        parent::__construct($persistence);
    }

    protected function init(): void
    {
        parent::init();

        $this->addField('name');
        $this->addField('emails', ['type'=>'text']);

        $this->addUserAction('send_invite', ['args'=>['type'=>new Poll()]]);
    }
}
