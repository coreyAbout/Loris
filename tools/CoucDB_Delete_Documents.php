<?php

//first draft, should take parameters instead of the hardcoded 'instruments','tsi' instrument name, etc

$result = file_get_contents('http://localhost:5984/prevent_ad_dqg/_design/DQG-2.0/_view/instruments?startkey=["tsi","MTL0000","000000"]&endkey=["tsi","MTL9999","ZZZZZZ"]&reduce=false');
$json = json_decode($result);
$json = $json->rows;

foreach ($json as $key => $value) {

    $id = $value->id;
    $result = file_get_contents("http://localhost:5984/prevent_ad_dqg/". $id);
    $j = json_decode($result);
    $rev = $j->_rev;

    echo 'curl -X DELETE "localhost:5984/prevent_ad_dqg/' . $id . '?rev=' . $rev . '";';
    echo "\n";

}

?>
