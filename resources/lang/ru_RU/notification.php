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
        'subject' => 'Голосование ":title" с :start по :end на платформе "Альбо"',
        'message' => 'Вы приглашены принять участие в голосовании ":title" на платформе “Альбо” с :start по :end.',
        'attention' => 'Внимание!',
        'attentionDescription' => 'Не передавайте другим лицам содержимое этого письма, чтобы не утратить возможность лично проголосовать.',
        'whenInvitationExpires' => 'Приглашение действует, пока ее получатель не проголосует.',
        'vote' => 'Голосовать',
        'mistake' => 'Если Вы не желаете участвовать в этом голосовании, просто проигнорируйте это письмо. ',
        'autoLetter' => 'Письмо создано автоматически, пожалуйста, не пытайтесь отвечать на него.',
    ],
];
