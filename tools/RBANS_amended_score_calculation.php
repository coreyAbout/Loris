<?php

/*
 * This script is to add:
 * 4 points to semantic_fluency_score for all RBANS Test Version C and
 * 2 points to semantic_fluency_score for all RBANS Test Version D
*/

//inform RAs to halt
//select commentid,semantic_fluency_score from RBANS where test_version='C' and semantic_fluency_score is not null;
//semantic_fluency_score = semantic_fluency_score + 4;
//update semantic_fluency_score where commentid;
//select commentid,semantic_fluency_score from RBANS where test_version='D' and semantic_fluency_score is not null;
//semantic_fluency_score = semantic_fluency_score+2;
//update semantic_fluency_score where commentid
//run mass score script;
//inform RAs to continue

require_once "../vendor/autoload.php";
set_include_path(get_include_path().":../project/libraries:../php/libraries:");
require_once "../php/libraries/NDB_Client.class.inc";

$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize('../project/config.xml');

$DB =& Database::singleton();

//C
$rows = $DB->pselect("select commentid,semantic_fluency_score from RBANS where test_version='C' and semantic_fluency_score is not null",array());
$count = 0;
foreach ($rows as $row) {
 $count = $count + 1;
 $semantic_fluency_score = $row['semantic_fluency_score'] + 4;
 $commentid = array('commentid'=>$row['commentid']);
 $set = array('semantic_fluency_score'=>$semantic_fluency_score);

 $result = $DB->update("RBANS",$set,$commentid);
 echo 'count: ' . $count . ' updated: ' . $commentid['commentid'] . ' score was: ' . $row['semantic_fluency_score'] . ' now it is: ' . $semantic_fluency_score . "\n";
}

echo "\n";
echo "******************************************************************";
echo "\n";
echo "\n";

//D
$rows = $DB->pselect("select commentid,semantic_fluency_score from RBANS where test_version='D' and semantic_fluency_score is not null",array());
$count = 0;
foreach ($rows as $row) {
 $count = $count + 1;
 $semantic_fluency_score = $row['semantic_fluency_score'] + 2;
 $commentid = array('commentid'=>$row['commentid']);
 $set = array('semantic_fluency_score'=>$semantic_fluency_score);

 $result = $DB->update("RBANS",$set,$commentid);
 echo 'count: ' . $count . ' updated: ' . $commentid['commentid'] . ' score was: ' . $row['semantic_fluency_score'] . ' now it is: ' . $semantic_fluency_score . "\n";
}

?>
