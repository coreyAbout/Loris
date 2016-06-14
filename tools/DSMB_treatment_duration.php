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

$dsmb = array("MTL0001","MTL0002","MTL0003","MTL0004","MTL0005","MTL0007","MTL0014","MTL0015","MTL0017","MTL0018","MTL0023","MTL0024","MTL0025","MTL0026","MTL0027","MTL0029","MTL0032","MTL0033","MTL0036","MTL0037","MTL0038","MTL0039","MTL0040","MTL0041","MTL0047","MTL0048","MTL0049","MTL0054","MTL0060","MTL0064","MTL0065","MTL0067","MTL0068","MTL0070","MTL0074","MTL0078","MTL0079","MTL0080","MTL0084","MTL0085","MTL0086","MTL0091","MTL0092","MTL0097","MTL0101","MTL0102","MTL0106","MTL0107","MTL0110","MTL0111","MTL0112","MTL0113","MTL0122","MTL0124","MTL0125","MTL0126","MTL0133","MTL0135","MTL0136","MTL0137","MTL0138","MTL0139","MTL0150","MTL0154","MTL0157","MTL0159","MTL0160","MTL0165","MTL0166","MTL0168","MTL0169","MTL0172","MTL0173","MTL0174","MTL0180","MTL0181","MTL0184","MTL0186","MTL0191","MTL0196","MTL0197","MTL0199","MTL0201","MTL0202","MTL0203","MTL0205","MTL0206","MTL0207","MTL0212","MTL0214","MTL0217","MTL0219","MTL0223","MTL0225","MTL0226","MTL0227","MTL0228","MTL0231","MTL0232","MTL0234","MTL0235","MTL0242","MTL0244","MTL0248","MTL0254","MTL0257","MTL0261","MTL0262","MTL0263","MTL0264","MTL0265","MTL0268","MTL0269","MTL0270","MTL0271","MTL0273","MTL0274","MTL0276","MTL0278","MTL0279","MTL0281","MTL0282","MTL0284","MTL0285","MTL0287","MTL0288","MTL0290","MTL0291","MTL0292","MTL0294","MTL0295","MTL0296","MTL0297","MTL0298","MTL0300","MTL0304","MTL0305","MTL0307","MTL0308","MTL0310","MTL0311","MTL0314","MTL0316","MTL0318","MTL0320","MTL0321","MTL0322","MTL0324","MTL0325","MTL0326","MTL0327","MTL0330","MTL0331","MTL0333","MTL0334","MTL0337","MTL0339","MTL0341","MTL0343","MTL0347","MTL0348","MTL0349","MTL0350","MTL0352","MTL0353","MTL0354","MTL0356","MTL0357","MTL0358","MTL0360","MTL0361","MTL0363","MTL0365","MTL0366","MTL0367","MTL0370","MTL0371","MTL0373","MTL0374","MTL0375","MTL0376","MTL0377","MTL0381","MTL0382","MTL0383","MTL0384","MTL0385","MTL0386","MTL0388","MTL0390","MTL0391","MTL0392","MTL0393","MTL0394","MTL0397","MTL0398","MTL0401","MTL0402","MTL0403","MTL0405","MTL0409","MTL0412","MTL0414","MTL0415","MTL0420","MTL0423","MTL0424","MTL0425","MTL0427","MTL0431","MTL0432","MTL0435","MTL0441","MTL0442","MTL0443","MTL0447","MTL0448","MTL0450","MTL0453","MTL0455","MTL0457","MTL0472","MTL0082");
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
 $startDate = "00-00-0000";
}
//                $lastDate = strtoupper(date("dMY", strtotime($lastDate)));

$myfile = "treatment_duration.dat";
//fopen("dsmb.txt", "w") or die("Unable to open file!");
$txt = $pscid . "\t" . $startDate . "\t" . "00-00-0000" . "\t" . "0" . "\n";
file_put_contents($myfile, $txt, FILE_APPEND);
//fclose($myfile);

            }
}



