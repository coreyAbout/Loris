<?php

require_once "generic_includes.php";
require_once "NDB_BVL_InstrumentStatus.class.inc";
require_once "NDB_BVL_Instrument.class.inc";
require_once "User.class.inc";
require_once "Utility.class.inc";
require_once "NDB_Client.class.inc";

$config = NDB_Config::singleton();

$select = $DB->pselect("select c.PSCID, c.CandID, 13_cigarette_smoking, 14_cigar_smoking, 15_pipe_smoking from general_information as g join flag as f on (g.commentid=f.commentid) join session as s on (f.sessionid=s.id) join candidate as c on (s.candid=c.candid) where g.commentid not like 'DDE%' order by c.PSCID asc",array());

foreach ($select as $key=>$value) {
 if ($value['13_cigarette_smoking'] == 'never' && $value['14_cigar_smoking'] == 'never' && $value['15_pipe_smoking'] == 'never') {
  echo $value['PSCID'] . "\t" . $value['CandID'] . "\t" . "never" . "\n";
 } elseif ($value['13_cigarette_smoking'] == 'yes' || $value['14_cigar_smoking'] == 'yes' || $value['15_pipe_smoking'] == 'yes') {
  echo $value['PSCID'] . "\t" . $value['CandID'] . "\t" . "currently" . "\n";
 } else {
  echo $value['PSCID'] . "\t" . $value['CandID'] . "\t" . "past" . "\n";
 }
}



?>
