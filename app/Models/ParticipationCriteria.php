<?php


namespace App\Models;


use Atk4\Data\Persistence;

class ParticipationCriteria extends \Atk4\Data\Model
{
    public $table = 'participation_criteria';
    public $title_field = 'check';
    public function __construct(Persistence $persistence = null)
    {
        parent::__construct($persistence);
    }

    protected function init(): void
    {
        parent::init();

        $this->hasOne('poll_id', ['model'=>new Poll()]);

        $this->addField('check', [
            'enum'=>['document_ownership', 'mvp_token'],
            'default'=>'mvp_token'
        ]);
        // Will discuss with Mark
        $this->addField('document_type');
        $this->addField('document_authority');
        $this->addField('mvp_token_seed', [
            //'editable'=>false
        ]);

    }
}
