<?php
global $mc;
global $app;
include 'init.php';
$mc->setModel(new \App\Models\EmailList($app->db), [
]);

