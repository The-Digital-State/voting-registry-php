<?php


namespace App\Models;


use Atk4\Data\Persistence;

class ParticipationCriteria extends \Atk4\Data\Model
{
    public $table = 'participation_criteria';
    public function __construct(Persistence $persistence = null)
    {
        parent::__construct($persistence);
    }

    protected function init(): void
    {
        parent::init();

        $this->addField('name');
        $this->addField('check', [
            'enum'=>['document_ownership']
        ]);
    }
}
