<?php

use Phpnova\Database\db;

require __DIR__ . '/../vendor/autoload.php';


// $pdo = db::connect()->mysql('root', '', 'la_casa_imperial');


// db::registerConnection('test', $pdo);
$client = db::connect()->mysql('root', '', 'la_casa_imperial', timezone: '-05:00');

// // $res = $client->table('tb_persons')->insert(['dni' => '10027244088', 'type' => 1], '*');
// $res = $client->table('tb_persons')->update(['type' => 0], 'dni = ?', ['1007244088']);
// $res = $client->table('tb_persons')->delete('dni = ?', ['1007244088']);

$res = db::table('tb_persons')->getAll();

header('content-type: application/json');

echo json_encode($res);