<?php
global $mc;
global $app;
include 'init.php';
$mc->setModel(new \App\Models\Poll($app->db), [
    'Choices'=>[], 'Votes'=>[], 'ParticipationCriterias'=>[]
]);

