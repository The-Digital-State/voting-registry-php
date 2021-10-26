<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notifications Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'invitation' => [
        'subject' => 'Voting ":title" from :start to :end on the Albo platform',
        'message' => 'You are invited to take part in the voting ":title" on the "Albo" platform :start to :end.',
        'attention' => 'Attention!',
        'attentionDescription' => 'Do not share the contents of this letter with others, so as not to lose the opportunity to vote.',
        'whenInvitationExpires' => 'The invitation is valid until the recipient votes.',
        'vote' => 'Vote',
        'mistake' => 'If you do not wish to participate in this vote, simply ignore this letter.',
        'autoLetter' => 'The email is automatically generated, please do not try to reply to it.',
    ],
];
