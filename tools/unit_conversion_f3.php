<?php

require_once "generic_includes.php";
require_once "NDB_BVL_InstrumentStatus.class.inc";
require_once "NDB_BVL_Instrument.class.inc";
require_once "User.class.inc";
require_once "Utility.class.inc";
require_once "NDB_Client.class.inc";

/*
This is a script for one time use only, unless the UPDATE statements
are commented out before running. It is because at one point data entry 
was possible with differing units, and it was unified to use the 
metric system only. It converts existing non-metric units and values 
in the Follow Up into metric system. It also 'softly' compares against 
values from the General Physical in Eligibility visit

Script has been adapted from unit_conversion.php
*/

$config = NDB_Config::singleton();

$candidates= $DB->pselect("SELECT CandID FROM candidate where candid!=702711 and candid!=424640 and candid!=576695 and candid!=175961 and candid!=998877 order by PSCID", array());

foreach ($candidates as $key=>$value) {
 echo "\n";
 $id_visit_label = $DB->pselect("SELECT ID,Visit_label,Current_stage from session where candid!=702711 and candid!=424640 and candid!=576695 and candid!=175961 and candid!=998877 and candid=:candidateid",array('candidateid'=>$value['CandID']));

 $f3commentIDs = array();
 foreach ($id_visit_label as $key2=>$value2) {
  if ($value2['Current_stage'] != 'Not Started' && strpos($value2['Visit_label'], 'FU') !== FALSE) {
   $f3commentIDs[] = $DB->pselectOne("select commentid from flag where test_name='f3' and commentid not like 'DDE%' and sessionid=:sid",array('sid'=>$value2['ID']));
  }
 }

 $current_weight = 0;
 $current_weight_units = '';
 foreach ($id_visit_label as $key2=>$value2) {
  if ($value2['Current_stage'] != 'Not Started' && ($value2['Visit_label'] === 'PREEL00')) {
   $gen_phys_commentid = $DB->pselect("select commentid from flag where test_name='general_physical' and commentid not like 'DDE%' and sessionid=:sid",array('sid'=>$value2['ID']));
//just a precaution
if (count($gen_phys_commentid) > 1) die();
   foreach ($gen_phys_commentid as $key3=>$value3) {
    $current_weight = $DB->pselectOne("select 4_weight from general_physical where commentid='{$value3['commentid']}'",array());
    $current_weight_units = $DB->pselectOne("select 4_weight_units from general_physical where commentid='{$value3['commentid']}'",array());
   }
  }
 }

// print_r($f3commentIDs);
 foreach ($f3commentIDs as $key4=>$value4) {
  $f3_weight = $DB->pselectOne("select 12_weight from f3 where commentid='{$value4}'",array());
  $f3_weight_units = $DB->pselectOne("select 12_weight_units from f3 where commentid='{$value4}'",array());
  if ($f3_weight!=NULL && $f3_weight!='not_answered' && $f3_weight_units == 'lbs') {
   $new_weight = round($f3_weight * 0.45359237,1);
   $new_weight_units = "kg";
   $weight_difference = 0;
   if ($current_weight > 0 && $current_weight != NULL && $current_weight != 'not_answered') {
    $weight_difference = $new_weight / $current_weight * 100;
   }
   if ($weight_difference > 105 || $weight_difference < 95) {
    echo "*** >105 || <95 *** " . $value4 . " " . $f3_weight . $f3_weight_units . " converted to " . $new_weight . $new_weight_units . " | Accuracy: " . $weight_difference . "%";echo "\n";
   } else {
    echo $value4 . " " . $f3_weight . $f3_weight_units . " converted to " . $new_weight . $new_weight_units . " | Accuracy: " . $weight_difference . "%";echo "\n";
   }

/*
      $success = $DB->update("f3", array('12_weight'=>$new_weight), array('commentid'=>$value4));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update {$value4}\n".$success->getMessage()."\n");
          return false;
      }
      $success = $DB->update("f3", array('12_weight_units'=>$new_weight_units), array('commentid'=>$value4));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update {$value4}\n".$success->getMessage()."\n");
          return false;
      }
//update DDE(all corresponding DDE were Complete at this point in time so updating all corresponding entries)
      $DDECommentID = 'DDE_' . $value4;
      $success = $DB->update("f3", array('12_weight'=>$new_weight), array('commentid'=>$DDECommentID));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update {$value4}\n".$success->getMessage()."\n");
          return false;
      }
      $success = $DB->update("f3", array('12_weight_units'=>$new_weight_units), array('commentid'=>$DDECommentID));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update {$value4}\n".$success->getMessage()."\n");
          return false;
      }
*/

  }
 }

}
?>
