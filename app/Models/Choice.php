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

        $this->hasOne('poll_id', ['model'=>Models\Poll::class])
            ->withTitle()
            ->addFields([
                'poll_type'=>'type',
                'poll_status'=>'status',
            ]);
        $this->addHook([$this::HOOK_BEFORE_SAVE, $this::HOOK_BEFORE_DELETE], function ($m){
            if($m['poll_status'] != 'draft') {
                throw (new Core\Exception('Poll is public and candidates cannot be changed'))
                    ->addMoreInfo('poll', $m);
            }
        });
        $this->addField('name');

        // For Elections - this should contain candidate's reference document. Owner of this
        // document will be able to receive a certificate of winning election
        $this->addField('candidate_document_id');
    }
}
