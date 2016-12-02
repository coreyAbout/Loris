<?php

require_once "generic_includes.php";
$config =& NDB_Config::singleton();
$db =& Database::singleton();

if (($handle = fopen("/tmp/check_LP_visits.csv", "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $PSCID = $row[0];
        $visit = $row[1];

        $query = "SELECT 'X' FROM session JOIN candidate USING (CandID) WHERE visit_label='$visit' AND PSCID='$PSCID'";
        $result = $db->pselectOne($query, array());

        if ($result !== "X") {
            echo "$PSCID missing $visit\n";
        }
    }
    fclose($handle);
}

?>
