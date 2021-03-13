<?php


namespace App\Models;


use Atk4\Core;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\ValidationException;

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

        // Basic information about Vote
        $this->addField('title');
        $this->addField('description');

        // Types explained:
        //  - Vote could be binary or multiple choices, the outcome is a decision
        //    on activating a specific process. Result is a single outcome.
        // - Election will have candidates as multiple choices, and the outcome
        //    will provide "N" winners by top number of votes
        // - Petition will have only a single choice. Petition defines a threshold
        //    when it becomes 'open'
        $this->addField('type', [
            'enum'=>['vote', 'election', 'Petition'],
            'description'=>'Vote selects a single choice. Election will permit multiple candidates to win. '.
                'Petition has one choice and vote threshold when it becomes public'
        ]);
        $this->addField('elected_candidates', [
            'type'=>'numeric',
            'description'=>'for type=Election, how many candidates must be chosen from the list'
        ]);
        $this->addField('petition_threshold', [
            'type'=>'numeric',
            'description'=>'How many votes are needed, before voting envelopes will open'
        ]);


        // Poll starts in 'draft' status and editing is allowed at this time.
        $this->addField('status', [
            'enum'=>['draft', 'public', 'finished'],
            'default'=>'draft'
        ]);
        $this->addCalculatedField('active', function($m){
            $ts = new \DateTime();
            return $m['status'] === 'public' && $m['start'] < $ts && $ts < $m['finish'];
        });
        $this->addUserAction('publish');
        $this->addHook($this::HOOK_BEFORE_SAVE, function ($m){
            if($m['status'] != 'draft') {
                throw (new Core\Exception('Poll is public and cannot be changed'))
                    ->addMoreInfo('poll', $m);
            }
        });
        $this->addUserAction('vote');

        $this->addField('start', ['type'=>'datetime']);
        $this->addField('end', ['type'=>'datetime']);

        $this->hasMany('Choices');
        $this->hasMany('ParticipationCriterias');

//
//        // questions = [ { question: 'who', options: [ { option: 'john' } ] } ]
//        $this->containsMany('questions', [
//            'caption' => 'Poll Questions',
//            'model' => new Class extends Model {
//                public function init():void {
//                    parent::init();
//                    $this->addField('question');
//                    $this->containsMany('options', [
//                        'caption' => 'Available Options',
//                        'model' => new Class extends Model {
//                            public function init():void {
//                                parent::init();
//                                $this->addField('option');
//                            }
//                        }
//                    ]);
//                }
//            }
//        ]);
//
//        $this->containsMany('participantList', [
//            'caption' => 'Participant Links',
//            'model' => new Class extends Model {
//                public function init():void {
//                    parent::init();
//                    $this->hasOne('participant')
//                        ->addFields(['name']);
//                }
//            }
//        ]);
    }

}
