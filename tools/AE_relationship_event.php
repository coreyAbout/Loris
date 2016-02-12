<?php 
/*
Script that was used to pull relationship & event results for Adverse Events
And into csv format
*/

require_once "generic_includes.php";
require_once "PEAR.php";

for ($i=1; $i<51; $i++) {
                        
    $config =& NDB_Config::singleton();
    $db =& Database::singleton();
    $result = $db->pselect("SELECT substring(adverse_events.commentid,7,7) as pscid,visit_label,{$i}_event,{$i}_relationship from adverse_events join flag using (commentid) join session on (session.id=flag.sessionid) where ({$i}_relationship='4_possibly_related' or {$i}_relationship='5_related') and adverse_events.commentid not like 'DDE%' and visit_label='NAPAE00' order by pscid");

    foreach($result as $key=>$value) {

        $rez = implode(',', $value);
        echo $rez . "\n";

    }

}

?>
