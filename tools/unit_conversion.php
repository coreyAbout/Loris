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
into metric units. It also 'softly' compares against MRI values from 
the Enrollment visit

The scope of this script has changed over time and is therefore very messy.
Can be better rewritten in the year 2020
*/

$config = NDB_Config::singleton();

$candidates= $DB->pselect("SELECT CandID FROM candidate", array());

foreach ($candidates as $key=>$value) {
 $id_visit_label = $DB->pselect("SELECT ID,Visit_label,Current_stage from session where candid!=702711 and candid!=424640 and candid!=576695 and candid!=175961 and candid!=998877 and candid=:candidateid",array('candidateid'=>$value['CandID']));
 foreach ($id_visit_label as $key2=>$value2) {
  $mri_visit = 0;
  if ($value2['Current_stage'] != 'Not Started' && ($value2['Visit_label'] === 'PREEN00' || $value2['Visit_label'] === 'NAPEN00')) {
   $mri_visit = $value2['ID'];
   echo "MRI SessionID is " . $mri_visit;
   echo "\n";
   $mri_visit_date = $DB->pselectOne("SELECT DATE_FORMAT(pf.Value,'%Y-%m-%d') FROM parameter_type pt JOIN parameter_file pf ON pf.ParameterTypeID=pt.ParameterTypeID join files ff on (pf.FileID=ff.FileID) WHERE Name='acquisition_date' and SourceFrom='parameter_file' AND SessionID=:sid",array('sid'=>$value2['ID']));
   break;
  }
 }
 $no_mri = false;
 foreach ($id_visit_label as $key2=>$value2) {
  if ($value2['Current_stage'] != 'Not Started' && ($value2['Visit_label'] === 'PREEL00')) {
   $gen_phys_commentid = $DB->pselect("select commentid from flag where test_name='general_physical' and commentid not like 'DDE%' and sessionid=:sid",array('sid'=>$value2['ID']));
   $mri_weight = $DB->pselectOne("SELECT pf.Value FROM parameter_type pt JOIN parameter_file pf ON pf.ParameterTypeID=pt.ParameterTypeID join files on (pf.FileID=files.FileID) WHERE Name='patient:weight' AND SourceFrom='parameter_file' AND SessionID=:sid",array('sid'=>$mri_visit));
   $mri_height = $DB->pselectOne("SELECT pf.Value FROM parameter_type pt JOIN parameter_file pf ON pf.ParameterTypeID=pt.ParameterTypeID join files on (pf.FileID=files.FileID) WHERE Name='dicom_0x0010:el_0x1020' AND SourceFrom='parameter_file' AND SessionID=:sid",array('sid'=>$mri_visit));
   if (is_array($mri_visit_date) || is_array($mri_weight) || is_array($mri_height)) {
    $no_mri = true;
   } else {
    //numerize with decimals
    $mri_weight = (float)$mri_weight * 1.0;
    $mri_height = (float)$mri_height * 1.0;
    //if height is in meters...
    if ($mri_height < 3.0)
     $mri_height = $mri_height * 100.0;
   }
//just a precaution
if (count($gen_phys_commentid) > 1) die();
   foreach ($gen_phys_commentid as $key3=>$value3) {
    $gen_phys_commentid_date = $DB->pselectOne("select date_taken from general_physical where commentid='{$value3['commentid']}'",array());
//print ($mri_visit_date);print($gen_phys_commentid_date);
    if (!$no_mri) {
     $date1=date_create($mri_visit_date);
     $date2=date_create($gen_phys_commentid_date);
     $diff=date_diff($date1,$date2,TRUE);
    }

    $old_weight = $DB->pselectOne("select 4_weight from general_physical where commentid='{$value3['commentid']}'",array());
    $old_weight_units = $DB->pselectOne("select 4_weight_units from general_physical where commentid='{$value3['commentid']}'",array());
    $old_height = $DB->pselectOne("select 5_height from general_physical where commentid='{$value3['commentid']}'",array());
    $old_height_units = $DB->pselectOne("select 5_height_units from general_physical where commentid='{$value3['commentid']}'",array());
    if ($old_weight_units!=null && $old_weight!='not answered' && $old_weight_units === 'lb') {
      $new_weight = round($old_weight * 0.45359237,1);
      $new_weight_units = "kg";
      $weight_difference = 0;
      if (!is_array($mri_weight)) {
       $weight_difference = $new_weight / $mri_weight * 100;
      }
/*      $success = $DB->update("general_physical", array('4_weight'=>$new_weight), array('commentid'=>$value3['commentid']));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update $value3['commentid']\n".$success->getMessage()."\n");
          return false;
      }
      $success = $DB->update("general_physical", array('4_weight_units'=>$new_weight_units), array('commentid'=>$value3['commentid']));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update $value3['commentid']\n".$success->getMessage()."\n");
          return false;
      }
*/
if ($no_mri) {
      echo $value3['commentid'] . " " . $old_weight . $old_weight_units . " converted to " . $new_weight . $new_weight_units . " | No MRI";echo "\n";
} elseif ($weight_difference > 200.00) {
      echo "*** >200 *** " . $value3['commentid'] . " " . $old_weight . $old_weight_units . " converted to " . $new_weight . $new_weight_units . " | MRI: " . $mri_weight . " | Accuracy: " . $weight_difference . "%" . " | Elapsed time between: " . $diff->format("%R%a days");echo "\n";
} elseif ($weight_difference > 102.00 || $weight_difference < 98.00) {
      echo "*** >102 || <98 *** " . $value3['commentid'] . " " . $old_weight . $old_weight_units . " converted to " . $new_weight . $new_weight_units . " | MRI: " . $mri_weight . " | Accuracy: " . $weight_difference . "%" . " | Elapsed time between: " . $diff->format("%R%a days");echo "\n";
} else {
      echo $value3['commentid'] . " " . $old_weight . $old_weight_units . " converted to " . $new_weight . $new_weight_units . " | MRI: " . $mri_weight . " | Accuracy: " . $weight_difference . "%" . " | Elapsed time between: " . $diff->format("%R%a days");echo "\n";
}
    }
    if ($old_height_units!=null && $old_height!='not answered' && $old_height_units === 'in') {
      $new_height = round($old_height / 0.39370,1);
      $new_height_units = "cm";
      $height_difference = 0;
      if (!is_array($mri_height)) {
       $height_difference = $new_height / $mri_height * 100;
      }
if ($no_mri) {
      echo $value3['commentid'] . " " . $old_height . $old_height_units . " converted to " . $new_height . $new_height_units . " | No MRI";echo "\n";
} elseif ($height_difference > 200.00) {
      echo "*** >200 *** " . $value3['commentid'] . " " . $old_height . $old_height_units . " converted to " . $new_height . $new_height_units . " | MRI: " . $mri_height . " | Accuracy: " . $height_difference , "%" . " | Elapsed time between: " . $diff->format("%R%a days");echo "\n";
} elseif ($height_difference > 102.00 || $height_difference < 98.00) {
      echo "*** >102 || <98 *** " . $value3['commentid'] . " " . $old_height . $old_height_units . " converted to " . $new_height . $new_height_units . " | MRI: " . $mri_height . " | Accuracy: " . $height_difference , "%" . " | Elapsed time between: " . $diff->format("%R%a days");echo "\n";
} else {
      echo $value3['commentid'] . " " . $old_height . $old_height_units . " converted to " . $new_height . $new_height_units . " | MRI: " . $mri_height . " | Accuracy: " . $height_difference , "%" . " | Elapsed time between: " . $diff->format("%R%a days");echo "\n";
}
/*      $success = $DB->update("general_physical", array('5_height'=>$new_height), array('commentid'=>$value3['commentid']));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update $value3['commentid']\n".$success->getMessage()."\n");
          return false;
      }
      $success = $DB->update("general_physical", array('5_height_units'=>$new_height_units), array('commentid'=>$value3['commentid']));
      if(PEAR::isError($success)) {
          fwrite(STDERR,"Error, to update $value3['commentid']\n".$success->getMessage()."\n");
          return false;
      }
*/
    }
   }
   echo "\n";
  }
 }

}

?>
