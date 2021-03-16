<?php


namespace App\Models;


use Atk4\Core;
use Atk4\Data\Persistence;
use App\Models;

class Choice extends \Atk4\Data\Model
{
    public $table = 'choice';
    public function __construct(Persistence $persistence = null)
    {
        parent::__construct($persistence);
    }

    protected function init(): void
    {
        parent::init();

        //$this->addField('test');
        $this->hasOne('poll_id', ['model'=>new Models\Poll()]);
        $this->hasMany('votes', ['model'=>new Models\Vote()])
            ->addField('votes', ['expr'=>'count(*)']);

//            ->addFields([
//                //'poll_type'=>'type',
//                'poll_status'=>'status',
//            ]);
//        $this->onHook($this::HOOK_BEFORE_SAVE, function ($m){
//            if($m->get('poll_status') != 'draft') {
//                throw (new Core\Exception('Poll is public and candidates cannot be changed'))
//                    ->addMoreInfo('poll', $m);
//            }
//        });
        $this->addField('name');

        // For Elections - this should contain candidate's reference document. Owner of this
        // document will be able to receive a certificate of winning election
//        $this->addField('candidate_document_id');
    }
}
