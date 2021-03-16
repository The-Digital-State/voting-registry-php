<?php
include '../../vendor/autoload.php';

$app = new \Atk4\Ui\App('Voting Admin');

$app->initLayout([\Atk4\Ui\Layout\Admin::class]);
$app->db = new \Atk4\Data\Persistence\Sql('mysql://root:hep6Ooch@localhost/voting-registry-php');
$app->layout->menuLeft->addItem(['Polls', 'icon'=>'dashboard'], ['index']);
$app->layout->menuLeft->addItem(['Lists', 'icon'=>'group'], ['lists']);
$mc = $app->add([
Atk4\MasterCrud\MasterCRUD::class,
#'ipp' => 5,
#    'quickSearch' => ['name'],
]);
