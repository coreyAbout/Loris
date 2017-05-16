<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once "generic_includes.php";

# will make this look better in the year 2018

date_default_timezone_set('America/Montreal');

$file = 'treatment_duration.dat';
// The new person to add to the file
$person = "Alloc" . "\t" . "StartDate" . "\t" . "DateReturned" . "\t" . "TD" . "\n";
// Write the contents to the file, 
// using the FILE_APPEND flag to append the content to the end of the file
// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
file_put_contents($file, $person, FILE_APPEND | LOCK_EX);



            $db =& Database::singleton();

$dsmb = array("MTL0001","MTL0002", "...");
//$dsmb = array("MTL0354", "MTL0298");
foreach ($dsmb as $d) {
$datetime1 = null;
$datetime2 = null;
$interval = null;
$lastDate = null;
$startDate = null;
$commID = null;
$termination = null;
$pscid = null;
$candid = null;

$candid = $db->pselectOne("SELECT candid from candidate where pscid='$d'", array());
$pscid = $d;

            $completed = $db->pselectOne("SELECT drug_returned_date from drug_compliance where candid=$candid and visit_label='NAPFU24' and drug_returned_date!='0000-00-00' and drug_returned_date is not null and drug_returned_date!=''",array());
            if (!empty($completed)) {
                $startDate = $db->pselectOne("SELECT drug_issued_date from drug_compliance where candid=$candid and drug_issued_date!='0000-00-00' and drug_issued_date is not null and drug_issued_date!='' order by drug_issued_date asc",array());
                $lastDate = $completed;
                    if (!empty($lastDate) && !empty($startDate)) {
                        $datetime1 = new DateTime($startDate);
                        $datetime2 = new DateTime($lastDate);
                        $interval = $datetime1->diff($datetime2,true);
                    }
            } else {
                for ($i = 1; $i <= 30; $i++) {
                    $commID = $db->pselectOne("select treatment_interruption.commentid from treatment_interruption join flag using (commentid) join session on (session.id=flag.sessionid) where treatment_interruption.commentid not like 'DDE%' and candid=$candid and test_name='treatment_interruption'",array());
                    $termination = $db->pselectOne("SELECT {$i}_treatment_termination from treatment_interruption where CommentID='$commID'",array());
                    if ($termination == 'yes') {
                        $lastDate = $db->pselectOne("SELECT {$i}_interruption_from_date from treatment_interruption where CommentID='$commID'",array());
                        $startDate = $db->pselectOne("SELECT drug_issued_date from drug_compliance where candid=$candid and drug_issued_date!='0000-00-00' and drug_issued_date is not null and drug_issued_date!='' order by drug_issued_date asc",array());
                        if (!empty($lastDate) && !empty($startDate)) {
                            $datetime1 = new DateTime($startDate);
                            $datetime2 = new DateTime($lastDate);
                            $interval = $datetime1->diff($datetime2,true);
                            break;
                        }
                    }
                }
            }
//if ($pscid == "MTL0298")
//echo $startDate . $lastDate . $interval->days;
            if (is_object($interval)) {
                $startDate = strtoupper(date("dMY", strtotime($startDate)));
                $lastDate = strtoupper(date("dMY", strtotime($lastDate)));

$myfile = "treatment_duration.dat";
//fopen("dsmb.txt", "w") or die("Unable to open file!");
$txt = $pscid . "\t" . $startDate . "\t" . $lastDate . "\t" . $interval->days . "\n";
file_put_contents($myfile, $txt, FILE_APPEND);
//fclose($myfile);
                
            } else {
                        $startDate = $db->pselectOne("SELECT drug_issued_date from drug_compliance where candid=$candid and drug_issued_date!='0000-00-00' and drug_issued_date is not null and drug_issued_date!='' order by drug_issued_date asc",array());
if (!empty($startDate)) {
                $startDate = strtoupper(date("dMY", strtotime($startDate)));
} else {
 $startDate = "";
}
//                $lastDate = strtoupper(date("dMY", strtotime($lastDate)));

$myfile = "treatment_duration.dat";
//fopen("dsmb.txt", "w") or die("Unable to open file!");
$txt = $pscid . "\t" . $startDate . "\t" . "00-00-0000" . "\t" . "0" . "\n";
file_put_contents($myfile, $txt, FILE_APPEND);
//fclose($myfile);

            }
}



