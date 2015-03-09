<?php
/*
    make sure to set appropriate paths within script
    for each field in final_radiological_review, update corresponding field in radiology_review
*/

require_once "/home/jkat/neurodb/vendor/autoload.php";
set_include_path(get_include_path().":/home/jkat/neurodb/project/libraries:/home/jkat/neurodb/php/libraries:");
require_once "/home/jkat/neurodb/php/libraries/NDB_Client.class.inc";

$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize('/home/jkat/neurodb/project/config.xml');

$DB =& Database::singleton();

$reviews_done = $DB->pselect("SELECT * from final_radiological_review where Review_Done = '1'",array());
foreach ($reviews_done as $key=>$value) {
 print_r ($value);
// $success = $DB->update("radiology_review", $value, array('CommentID'=>$value['CommentID']));
    if (PEAR::isError($success)) {
        echo "Update unsuccessful\n";
        fwrite(STDERR, "Failed to update radiology_review, DB Error: " . $success->getMessage()."\n");
        return false;
    } else {
        echo "\n" . "Update successful on " . $value['CommentID'];
    }

}



?>
