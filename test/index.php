<?php

use Phpnova\Database\db;
require __DIR__ . '/../vendor/autoload.php';
#error_reporting(0);

try {
    
    $conn = db::connect()->mysql('root', '', 'mundo_adhesivos');

    db::connections()->register('la_casa_imperial', $conn);
    db::connections()->getDefault();

    // db::connections()->default();
    // $result = $conn->config->getTimezone();
    // $conn->config->setWritingStyleResultFields('camelcase');
    $result = $conn->table("vi_purchase_orders")->getAll();

    
    // $conection =  new DBConnection(null);

    // $conection->config->

    // $conection->beginTransaction();
    // $conection->commid();


  # Impirmimos el resultado en formato JSON
  header('content-type: application/json');
  echo json_encode($result, 128);

} catch (\Throwable $th) {
    //throw $th;

    header('content-type: text/plain');
    echo "Error\n";
    echo $th->getMessage();
    echo "\n" . $th->getFile();
    echo "\n" . $th->getLine();
}