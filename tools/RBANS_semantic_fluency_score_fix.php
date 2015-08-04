<?php

/*
 * This one time use script was to update the RBANS
 * semantic fluency score to use an updated scoring
 */

require_once __DIR__ . "/../vendor/autoload.php";
set_include_path(get_include_path().":../project/libraries:../php/libraries:");
require_once __DIR__ . "/../php/libraries/NDB_Client.class.inc";

$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize(__DIR__ . '/../project/config.xml');

$DB =& Database::singleton();

$results = $DB->pselect("SELECT commentID, test_version, semantic_fluency_score FROM RBANS",array());

foreach ($results as $result) {
    $raw = null;
    $adjusted = null;
    if ($result['test_version'] == 'A') {
        $raw = $result['semantic_fluency_score'];
        $adjusted = $result['semantic_fluency_score'];
    } elseif ($result['test_version'] == 'B') {
        $raw = $result['semantic_fluency_score'] - 4;
        $adjusted = $result['semantic_fluency_score'];
    } elseif ($result['test_version'] == 'C') {
        $raw = $result['semantic_fluency_score'];
        $adjusted = $result['semantic_fluency_score'] + 4;
    } elseif ($result['test_version'] == 'D') {
        $raw = $result['semantic_fluency_score'];
        $adjusted = $result['semantic_fluency_score'] + 2;
    }
    $DB->update('RBANS',array('semantic_fluency_score_raw'=>$raw, 'semantic_fluency_score'=>$adjusted),array('CommentID'=>$result['commentID']));
}

?>
