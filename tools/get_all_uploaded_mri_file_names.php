<?php

// gets all uploaded files from mri_parameter_form

require_once "../vendor/autoload.php";

require_once "NDB_Client.class.inc";
$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize();

$db =& Database::singleton();
$result = $db->pselect("SELECT BOLD_Encoding_Enrollment_physiological_data1_file_name, BOLD_Encoding_Enrollment_physiological_data2_file_name, BOLD_Encoding_Enrollment_task1_file_name, BOLD_Encoding_Enrollment_task2_file_name, BOLD_Retrieval_Enrollment_physiological_data1_file_name, BOLD_Retrieval_Enrollment_physiological_data2_file_name, BOLD_Retrieval_Enrollment_task1_file_name, BOLD_Retrieval_Enrollment_task2_file_name, BOLD_Resting_physiological_data1_file_name, BOLD_Resting_physiological_data2_file_name, BOLD_Resting_physiological_data3_file_name, BOLD_Resting_physiological_data4_file_name, BOLD_Encoding_physiological_data1_file_name, BOLD_Encoding_physiological_data2_file_name, BOLD_Encoding_task1_file_name, BOLD_Encoding_task2_file_name, BOLD_Retrieval_physiological_data1_file_name, BOLD_Retrieval_physiological_data2_file_name, BOLD_Retrieval_task1_file_name, BOLD_Retrieval_task2_file_name, ASL_physiological_data1_file_name FROM mri_parameter_form");

foreach ($result as $key => $value) {
    foreach ($value as $k => $v) {
        if ($v != '' && !is_null($v)) {        
            echo $v;
            echo "\n";
        }
    }
}


?>
