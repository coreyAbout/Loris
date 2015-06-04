<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once "generic_includes.php";
require_once "PEAR.php";

/*
Import script for Miranda's Auditory Processing data
This is to populate each applicable candidate with
their first PREAP00/NAPAP00 timepoint.
*/

$list = array(
//to add list of candidates and the corresponding visit
);

foreach ($list as $pscid => $visit_label) {
    echo "inserting session for candidate pscid: " . $pscid;
    $candid = $DB->pselectOne("SELECT CandID FROM candidate where PSCID=:pscid", array('pscid'=>$pscid));
    if (strpos($visit_label,'NAP') !== false) {
        $visit = "NAPAP00";
        $success = TimePoint::createNew($candid, 2, $visit);
        if(PEAR::isError($success)) {
            return PEAR::raiseError("create_timepoint::_process(): ".$success->getMessage());
        }
    } elseif (strpos($visit_label,'PRE') !== false) {
        $visit = "PREAP00";
        $success = TimePoint::createNew($candid, 1, $visit);
        if(PEAR::isError($success)) {
            return PEAR::raiseError("create_timepoint::_process(): ".$success->getMessage());
        }
    } else {
        echo "error on candidate pscid: " . $pscid;
    }
    echo "inserted " . $visit . ".";
    echo "\n";
}

?>

