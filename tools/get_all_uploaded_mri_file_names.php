<?php

// checks if files existing in database actually exists on file system

require_once "../vendor/autoload.php";

require_once "NDB_Client.class.inc";
$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize();

$db =& Database::singleton();
$result = $db->pselect("SELECT pscid,candid,visit_label,BOLD_Encoding_Enrollment_physiological_data1_file_name, BOLD_Encoding_Enrollment_physiological_data2_file_name, BOLD_Encoding_Enrollment_task1_file_name, BOLD_Encoding_Enrollment_task2_file_name, BOLD_Retrieval_Enrollment_physiological_data1_file_name, BOLD_Retrieval_Enrollment_physiological_data2_file_name, BOLD_Retrieval_Enrollment_task1_file_name, BOLD_Retrieval_Enrollment_task2_file_name, BOLD_Resting_physiological_data1_file_name, BOLD_Resting_physiological_data2_file_name, BOLD_Resting_physiological_data3_file_name, BOLD_Resting_physiological_data4_file_name, BOLD_Encoding_physiological_data1_file_name, BOLD_Encoding_physiological_data2_file_name, BOLD_Encoding_task1_file_name, BOLD_Encoding_task2_file_name, BOLD_Retrieval_physiological_data1_file_name, BOLD_Retrieval_physiological_data2_file_name, BOLD_Retrieval_task1_file_name, BOLD_Retrieval_task2_file_name, ASL_physiological_data1_file_name FROM mri_parameter_form join flag using (commentid) join session on (session.id=flag.sessionid) join candidate using (candid) where pscid not like '%MTL0000%' and pscid not like '%MTL999%'");

foreach ($result as $key => $value) {
    foreach ($value as $k => $v) {
        if ($k == 'pscid') {
            $pscid = $v;
        } elseif ($k == 'candid') {
            $candid = $v;
        } elseif ($k == 'visit_label') {
            $visit_label = $v;
        } elseif ($v != '' && !is_null($v)) {        
            $file = "/var/www/neurodb/htdocs/mri_parameter_directions/" . $candid . "_" . $visit_label . "/" . $v;
            if (!file_exists($file)) {
                echo "File: " . $file . " does not exist in the file system.\n";
            }
        }
    }
}


?>
