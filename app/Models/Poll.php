<?php


namespace App\Models;


use Atk4\Data\Model;
use Atk4\Data\Persistence;

class Poll extends \Atk4\Data\Model
{
    public $table = 'poll';
    public function __construct(Persistence $persistence = null)
    {
        parent::__construct($persistence);
    }

    protected function init(): void
    {
        parent::init();

        $this->addField('publicUid');
        $this->addField('status', ['enum'=>['DRAFT', 'LIVE']]);
        $this->addField('title');
        $this->addField('description');
        $this->addField('timeStart', ['type'=>'datetime']);
        $this->addField('timeEnd', ['type'=>'datetime']);
        $this->addField('created', ['type'=>'datetime']);
        $this->addField('updated', ['type'=>'datetime']);


        // questions = [ { question: 'who', options: [ { option: 'john' } ] } ]
        $this->containsMany('questions', [
            'caption' => 'Poll Questions',
            'model' => new Class extends Model {
                public function init():void {
                    parent::init();
                    $this->addField('question');
                    $this->containsMany('options', [
                        'caption' => 'Available Options',
                        'model' => new Class extends Model {
                            public function init():void {
                                parent::init();
                                $this->addField('option');
                            }
                        }
                    ]);
                }
            }
        ]);

        $this->containsMany('participantList', [
            'caption' => 'Participant Links',
            'model' => new Class extends Model {
                public function init():void {
                    parent::init();
                    $this->hasOne('participant')
                        ->addFields(['name']);
                }
            }
        ]);
    }

}
