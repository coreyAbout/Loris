<?php 
require_once "generic_includes.php";
require_once "PEAR.php";

$config =& NDB_Config::singleton();
$DB =& Database::singleton();
if(Utility::isErrorX($DB)) {
	fwrite(STDERR, "Could not connect to database: ".$DB->getMessage());
	return false;
}

$candidates = $DB->pselect("SELECT * from candidate",array());
foreach ($candidates as $candidate) {
       
          $candid =  $candidate['CandID'];
             $visits = $DB->pselect("SELECT * from session where CandID = :cid",array('cid'=>$candid));
$vis = array();
 foreach ($visits as $visit) {
array_push($vis,$visit['Visit_label']);
 }
 foreach ($visits as $visit) {
     if ($visit['Visit_label'] == 'NAPBL00'||$visit['Visit_label'] == 'PREBL00')
                if ((in_array('NAPFU12',$vis)) && (!in_array('NAPLP00',$vis)) && ((in_array('NAPBL00',$vis)) || (in_array('PREBL00',$vis)))) {
			$pscid = $DB->pselectOne("SELECT PSCID from candidate where CandID=:cid",array('cid'=>$candid));
                   print $pscid . " - " . $visit['Date_visit'] . " - " .  $visit['Visit_label'] . "\n";
                }
 }
}




?>
