<?php
require_once "generic_includes.php";
require_once "PEAR.php";

/*
Import script for the SMART study (Navigational task)
The data that comes in is a nightmare
*/

//exec("perl cleanup.pl EARLI-DATA-AOSI-1328311043.csv >/tmp/cleanup_xyz");
$fixedLines = file("/tmp/SMART_data_final_version.csv");
$fields = array();
$thisField = array();
$table1 = 'navigational_task_session_1'; //$argv[1];
$table2 = 'navigational_task_session_2'; //$argv[1];

$counter = 0;
$commentID1 = "";
$commentID2 = "";
$candID = "";
$PSCID = "";
$sessionID = "";
//$visit = "";


for( $i = 0; $i < sizeof($fixedLines); $i++ )
{
	if ($i == 0)
	{
		$key = explode(",", $fixedLines[$i]);

		for ($j = 0; $j < sizeof($key); $j++)
		{
			$field = $key[$j];
			$thisField[$field] = "";
		}
	}

	else
	{
		$fieldNum = 0;
		$data = explode(",", $fixedLines[$i]);


		foreach ($thisField as $key=>$value)
		{
			if (preg_match("/^\"/", $data[$fieldNum]) && preg_match("/\"$/", $data[$fieldNum])) {
				$data[$fieldNum] = preg_replace("/\"/", "", $data[$fieldNum]);
			}

			if (preg_match("/'/", $data[$fieldNum])) {
				$data[$fieldNum] = preg_replace("/'/", "\'", $data[$fieldNum]);
			}

			$thisField[$key] = $data[$fieldNum];
			$fieldNum++;
		}
		foreach ($thisField as $key=>$value)
		{
			if ($key == 'ID') {
				$counter ++;
				$PSCID = $thisField[$key];
				echo "-------count: " . $counter ."\n";
				echo "-------ID: " . $PSCID . "\n";
			}

elseif ($key == 'Session1_DateofTesting') {
$Session1_DateofTesting = $thisField[$key];
}
elseif ($key == 'Session1_PersonTested') {
$Session1_PersonTested = $thisField[$key];
}
elseif ($key == 'Session2_DateofTesting') {
$Session2_DateofTesting = $thisField[$key];
}
elseif ($key == 'Session2_PersonTested') {
$Session2_PersonTested = $thisField[$key];
}
elseif ($key == 'TestedBeforeMedicationGiven') {
$TestedBeforeMedicationGiven = $thisField[$key];
}
elseif ($key == 'WF_MeanPctDistanceError') {
$WF_MeanPctDistanceError = $thisField[$key];
}
elseif ($key == 'WF_MeanPctTimeError') {
$WF_MeanPctTimeError = $thisField[$key];
}
elseif ($key == 'WF_MeanPctAccuracy') {
$WF_MeanPctAccuracy = $thisField[$key];
}
elseif ($key == 'WF_MeanPctAccuracy3xIdealTime') {
$WF_MeanPctAccuracy3xIdealTime = $thisField[$key];
}
elseif ($key == 'MocaScore') {
$MocaScore = $thisField[$key];
}
elseif ($key == 'GDSscore') {
$GDSscore = $thisField[$key];
}
elseif ($key == 'TONI_IQ') {
$TONI_IQ = $thisField[$key];
}
elseif ($key == 'ROCopy') {
$ROCopy = $thisField[$key];
}
elseif ($key == 'ROImmediateRecall') {
$ROImmediateRecall = $thisField[$key];
}
elseif ($key == 'RODelayRecall') {
$RODelayRecall = $thisField[$key];
}
elseif ($key == 'RAVLTTotalRecall') {
$RAVLTTotalRecall = $thisField[$key];
}
elseif ($key == 'RAVLTListBRecall') {
$RAVLTListBRecall = $thisField[$key];
}
elseif ($key == 'RAVLTInterferenceRecall') {
$RAVLTInterferenceRecall = $thisField[$key];
}
elseif ($key == 'RAVLT30MinuteDelayRecall') {
$RAVLT30MinuteDelayRecall = $thisField[$key];
}
elseif ($key == 'RAVLTRecognitionListA') {
$RAVLTRecognitionListA = $thisField[$key];
}
elseif ($key == 'RAVLTRecognitionListB') {
$RAVLTRecognitionListB = $thisField[$key];
}
elseif ($key == 'RAVLTFalsePositives') {
$RAVLTFalsePositives = $thisField[$key];
}
elseif ($key == 'RAVLTMisattributions') {
$RAVLTMisattributions = $thisField[$key];
}
elseif ($key == 'DigitSymbolScore') {
$DigitSymbolScore = $thisField[$key];
}
elseif ($key == 'StroopCard1time') {
$StroopCard1time = $thisField[$key];
}
elseif ($key == 'StroopCard1errors') {
$StroopCard1errors = $thisField[$key];
}
elseif ($key == 'StroopCard2time') {
$StroopCard2time = $thisField[$key];
}
elseif ($key == 'StroopCard2errors') {
$StroopCard2errors = $thisField[$key];
}
elseif ($key == 'StroopCard3time') {
$StroopCard3time = $thisField[$key];
}
elseif ($key == 'StroopCard3errors') {
$StroopCard3errors = $thisField[$key];
}
elseif ($key == 'StroopInterference') {
$StroopInterference = $thisField[$key];
}
elseif ($key == 'TrailsPartAtime') {
$TrailsPartAtime = $thisField[$key];
}
elseif ($key == 'TrailsPartAerrors') {
$TrailsPartAerrors = $thisField[$key];
}
elseif ($key == 'TrailsPartBtime') {
$TrailsPartBtime = $thisField[$key];
}
elseif ($key == 'TrailsPartBerrors') {
$TrailsPartBerrors = $thisField[$key];
}
elseif ($key == 'PerceivedStressScore') {
$PerceivedStressScore = $thisField[$key];
}
elseif ($key == 'SantaBarbaraSenseofDirectionScaleScore') {
$SantaBarbaraSenseofDirectionScaleScore = $thisField[$key];
}
elseif ($key == 'QualityofLifeScore') {
$QualityofLifeScore = $thisField[$key];
}
elseif ($key == 'SelfEsteemQuestionnaireScore') {
$SelfEsteemQuestionnaireScore = $thisField[$key];
}
elseif ($key == 'BarrattImpulsivityScaleScore') {
$BarrattImpulsivityScaleScore = $thisField[$key];
}
elseif ($key == 'VM_4on8_CriteriaReached') {
$VM_4on8_CriteriaReached = $thisField[$key];
}
elseif ($key == 'VM_4on8_TTC') {
$VM_4on8_TTC = $thisField[$key];
}
elseif ($key == 'VM_4on8_LatencyTrial1') {
$VM_4on8_LatencyTrial1 = $thisField[$key];
}
elseif ($key == 'VM_4on8_ErrorsPart2ofTrial1') {
$VM_4on8_ErrorsPart2ofTrial1 = $thisField[$key];
}
elseif ($key == 'VM_4on8_LatencyTrial2') {
$VM_4on8_LatencyTrial2 = $thisField[$key];
}
elseif ($key == 'VM_4on8_ErrorsPart2ofTrial2') {
$VM_4on8_ErrorsPart2ofTrial2 = $thisField[$key];
}
elseif ($key == 'VM_4on8_LatencyTrial3') {
$VM_4on8_LatencyTrial3 = $thisField[$key];
}
elseif ($key == 'VM_4on8_ErrorsPart2ofTrial3') {
$VM_4on8_ErrorsPart2ofTrial3 = $thisField[$key];
}
elseif ($key == 'VM_4on8_ErrorsPart2ofABA') {
$VM_4on8_ErrorsPart2ofABA = $thisField[$key];
}
elseif ($key == 'VM_4on8_LatencyofABA') {
$VM_4on8_LatencyofABA = $thisField[$key];
}
elseif ($key == 'VM_4on8_AbsoluteProbeErrors') {
$VM_4on8_AbsoluteProbeErrors = $thisField[$key];
}
elseif ($key == 'VM_4on8_RotationalProbeErrors') {
$VM_4on8_RotationalProbeErrors = $thisField[$key];
}
elseif ($key == 'VM_4on8_TotalErrors') {
$VM_4on8_TotalErrors = $thisField[$key];
}
elseif ($key == 'VM_4on8_AverageErrors') {
$VM_4on8_AverageErrors = $thisField[$key];
}
elseif ($key == 'VM_4on8_AveragePart2ErrorsNoProbe') {
$VM_4on8_AveragePart2ErrorsNoProbe = $thisField[$key];
}
elseif ($key == 'VM_4on8_AveragePart2ErrorsAtrials') {
$VM_4on8_AveragePart2ErrorsAtrials = $thisField[$key];
}
elseif ($key == 'VM_4on8_ErrorsAminusB') {
$VM_4on8_ErrorsAminusB = $thisField[$key];
}
elseif ($key == 'VM_4on8_TotalLatency') {
$VM_4on8_TotalLatency = $thisField[$key];
}
else {
$VM_4on8_AverageLatency = str_replace("\n", "", $thisField[$key]);
}
/*
elseif ($key == 'VM_4on8_AverageLatencyPart2') {
$VM_4on8_AverageLatencyPart2 = $thisField[$key];
}
elseif ($key == 'VM_4on8_2GroupsStrategy') {
$VM_4on8_2GroupsStrategy = $thisField[$key];
}
elseif ($key == 'VM_4on8_4GroupsStrategy') {
$VM_4on8_4GroupsStrategy = $thisField[$key];
}

elseif ($key == 'GoNoGo_TTC') {
$GoNoGo_TTC = $thisField[$key];
}
elseif ($key == 'GoNoGo_CriteriaReached') {
$GoNoGo_CriteriaReached = $thisField[$key];
}
elseif ($key == 'GoNoGo_Probe1_PctCorrect') {
$GoNoGo_Probe1_PctCorrect = $thisField[$key];
}
elseif ($key == 'GoNoGo_Probe1and2_PctCorrect') {
$GoNoGo_Probe1and2_PctCorrect = $thisField[$key];
}
elseif ($key == 'GoNoGo_AllOpen1_PctCorrect') {
$GoNoGo_AllOpen1_PctCorrect = $thisField[$key];
}
elseif ($key == 'GoNoGo_AllOpen2_PctCorrect') {
$GoNoGo_AllOpen2_PctCorrect = $thisField[$key];
}
elseif ($key == 'Pairs_TTC') {
$Pairs_TTC = $thisField[$key];
}
elseif ($key == 'Pairs_TrialsAdministered') {
$Pairs_TrialsAdministered = $thisField[$key];
}
elseif ($key == 'Pairs_CriteriaReached') {
$Pairs_CriteriaReached = $thisField[$key];
}
elseif ($key == 'Pairs_AverageLatency') {
$Pairs_AverageLatency = $thisField[$key];
}
elseif ($key == 'Pairs_Probe1_PctCorrect') {
$Pairs_Probe1_PctCorrect = $thisField[$key];
}
elseif ($key == 'Pairs_Probe1and2_PctCorrect') {
$Pairs_Probe1and2_PctCorrect = $thisField[$key];
}
else {
$Pairs_AllOpen1_PctCorrect = str_replace("\n", "", $thisField[$key]);
} 
*/

}//closing last foreach







			$config =& NDB_Config::singleton();
			$db =& Database::singleton();
			if(Utility::isErrorX($db)) {
				fwrite(STDERR, "Could not connect to database: ".$db->getMessage());
				return false;
			}

			$candID = $db->pselectOne("SELECT CandID from candidate where PSCID =:pid", array("pid"=>$PSCID)); 


//insert into session1
			$visitno = $db->pselect("SELECT VisitNo from session where CandID =:cid", array("cid"=>$candID)); 
			$numVisits = count($visitno);

                        $insertData = array('ID'=>'','CandID'=>$candID,'CenterID'=>'2', 'VisitNo' =>$numVisits, 'Visit_label'=>'NAPNS01', 'UserID' => 'justin', 'Date_visit' => $Session1_DateofTesting, 'SubprojectID'=>'2','RegisteredBy'=>'justin');
			$updateData = array('CandID'=>$candID,'RegisteredBy'=>'justin','UserID' => 'justin', 'Date_visit' => $Session1_DateofTesting);
			if (!$db->pselect("SELECT Visit_label from session where Visit_label='NAPNS01' and CandID =:cid",array("cid"=>$candID))) {
				if (!empty($candID) && !empty($visitno)) {
					$success = $DB->insert('session', $insertData);
					if (Utility::isErrorX($success)) {
						fwrite(STDERR, "Failed to update session1 table, DB Error: " . $success->getMessage()."\n");
						return false;
					}
					echo "insert - Updated!\n";
				}
				else {
					echo "insert - Does not exist\n";
				}
			} else {
                                if (!empty($candID) && !empty($visitno)) {
					$sesID = $db->pselectOne("SELECT ID from session where Visit_label='NAPNS01' and CandID =:cid",array("cid"=>$candID));
                                      $success = $DB->update('session', $updateData,array('ID'=>$sesID));
                                        if (Utility::isErrorX($success)) {
                                                fwrite(STDERR, "Failed to update session1 table, DB Error: " . $success->getMessage()."\n");
                                                return false;
                                        }
                                      echo "update - Updated!\n";
                                }
                                else {
                                        echo "update - Does not exist\n";
                                }
			}

                       
//insert into flag1
			$sessionID = $db->pselectOne("SELECT ID from session where CandID =:cid and Visit_label =:visit", array('cid'=>$candID, 'visit'=>'NAPNS01'));
if (!empty($sessionID))
        $commentID1 = $candID . $PSCID . $sessionID . '2' . '79' . time();

                        $insertData = array('ID'=>'','SessionID'=>$sessionID,'Test_name'=>'navigational_task_session_1', 'CommentID'=>$commentID1,'Data_entry'=>'Complete','Administration'=>'All','UserID'=>'justin');
			$updateData = array('Data_entry'=>'Complete','Administration'=>'All','UserID'=>'justin');

                        if (!$db->pselect("SELECT SessionID from flag where SessionID =:sid",array("sid"=>$sessionID))) {
				if (!empty($sessionID)) {
					$success = $DB->insert('flag', $insertData);
					if (Utility::isErrorX($success)) {
						fwrite(STDERR, "Failed to update flag1 table, DB Error: " . $success->getMessage()."\n");
						return false;
					}
					echo "insert - Updated!\n";
				}
				else {
					echo "insert - Does not exist\n";
				}
			} else {
                                if (!empty($sessionID)) {
                                      $success = $DB->update('flag', $updateData,array('SessionID'=>$sessionID));
                                        if (Utility::isErrorX($success)) {
                                                fwrite(STDERR, "Failed to update flag1 table, DB Error: " . $success->getMessage()."\n");
                                                return false;
                                        }
                                      echo "update - Updated!\n";
                                }
                                else {
                                        echo "update - Does not exist\n";
                                }
			}
$commentID1 = $db->pselectOne("SELECT CommentID from flag where SessionID=:sid",array("sid"=>$sessionID));

//insert into session2
			$visitno = $db->pselect("SELECT VisitNo from session where CandID =:cid", array("cid"=>$candID)); 
			$numVisits = count($visitno);

 			$insertData = array('ID'=>'','CandID'=>$candID,'CenterID'=>'2', 'VisitNo' =>$numVisits, 'Visit_label'=>'NAPNS02','UserID' => 'justin','Date_visit' => $Session2_DateofTesting, 'SubprojectID'=>'2','RegisteredBy'=>'justin');
                        $updateData = array('CandID'=>$candID,'RegisteredBy'=>'justin','UserID' => 'justin', 'Date_visit' => $Session2_DateofTesting);
                        if (!$db->pselect("SELECT Visit_label from session where Visit_label='NAPNS02' and CandID =:cid",array("cid"=>$candID))) {
				if (!empty($candID) && !empty($visitno)) {
					$success = $DB->insert('session', $insertData);
					if (Utility::isErrorX($success)) {
						fwrite(STDERR, "Failed to update session2 table, DB Error: " . $success->getMessage()."\n");
						return false;
					}
					echo "insert - Updated!\n";
				}
				else {
					echo "insert - Does not exist\n";
				}
			} else {
                                if (!empty($candID) && !empty($visitno)) {
                                        $sesID = $db->pselectOne("SELECT ID from session where Visit_label='NAPNS02' and CandID =:cid",array("cid"=>$candID));
                                      $success = $DB->update('session', $updateData,array('ID'=>$sesID));
                                        if (Utility::isErrorX($success)) {
                                                fwrite(STDERR, "Failed to update session2 table, DB Error: " . $success->getMessage()."\n");
                                                return false;
                                        }
                                      echo "update - Updated!\n";
                                }
                                else {
                                        echo "update - Does not exist\n";
                                }
			}


//insert into flag2
			$sessionID = $db->pselectOne("SELECT ID from session where CandID =:cid and Visit_label =:visit", array('cid'=>$candID, 'visit'=>'NAPNS02'));
if (!empty($sessionID))
        $commentID2 = $candID . $PSCID . $sessionID . '2' . '80' . time();

                        $insertData = array('ID'=>'','SessionID'=>$sessionID,'Test_name'=>'navigational_task_session_2', 'CommentID'=>$commentID2,'Data_entry'=>'Complete','Administration'=>'All','UserID'=>'justin');
                        $updateData = array('Data_entry'=>'Complete','Administration'=>'All','UserID'=>'justin');

                        if (!$db->pselect("SELECT SessionID from flag where SessionID =:sid",array("sid"=>$sessionID))) {
				if (!empty($sessionID)) {
					$success = $DB->insert('flag', $insertData);
					if (Utility::isErrorX($success)) {
						fwrite(STDERR, "Failed to update flag2 table, DB Error: " . $success->getMessage()."\n");
						return false;
					}
					echo "insert - Updated!\n";
				}
				else {
					echo "insert - Does not exist\n";
				}
			} else {
                                if (!empty($sessionID)) {
                                      $success = $DB->update('flag', $updateData, array('SessionID'=>$sessionID));
                                        if (Utility::isErrorX($success)) {
                                                fwrite(STDERR, "Failed to update flag2 table, DB Error: " . $success->getMessage()."\n");
                                                return false;
                                        }
                                      echo "update - Updated!\n";
                                }
                                else {
                                        echo "update - Does not exist\n";
                                }
			}
$commentID2 = $db->pselectOne("SELECT CommentID from flag where SessionID=:sid",array("sid"=>$sessionID));



//exclude session start?
$mappings1 = array(
"CommentID"=>$commentID1,
"PSCID"=>$PSCID,
"Date_taken"=>$Session1_DateofTesting,
//"Session1_PersonTested"=>$Session1_PersonTested,
"TestedBeforeMedicationGiven"=>$TestedBeforeMedicationGiven,
"MocaScore"=>$MocaScore,
"GDSscore"=>$GDSscore,
"TONI_IQ"=>$TONI_IQ,
"DigitSymbolScore"=>$DigitSymbolScore,
"StroopCard1time"=>$StroopCard1time,
"StroopCard1errors"=>$StroopCard1errors,
"StroopCard2time"=>$StroopCard2time,
"StroopCard2errors"=>$StroopCard2errors,
"StroopCard3time"=>$StroopCard3time,
"StroopCard3errors"=>$StroopCard3errors,
"StroopInterference"=>$StroopInterference,
"TrailsPartAtime"=>$TrailsPartAtime,
"TrailsPartAerrors"=>$TrailsPartAerrors,
"TrailsPartBtime"=>$TrailsPartBtime,
"TrailsPartBerrors"=>$TrailsPartBerrors,
"VM_4on8_CriteriaReached"=>$VM_4on8_CriteriaReached,
"VM_4on8_TTC"=>$VM_4on8_TTC,
"VM_4on8_LatencyTrial1"=>$VM_4on8_LatencyTrial1,
"VM_4on8_ErrorsPart2ofTrial1"=>$VM_4on8_ErrorsPart2ofTrial1,
"VM_4on8_LatencyTrial2"=>$VM_4on8_LatencyTrial2,
"VM_4on8_ErrorsPart2ofTrial2"=>$VM_4on8_ErrorsPart2ofTrial2,
"VM_4on8_LatencyTrial3"=>$VM_4on8_LatencyTrial3,
"VM_4on8_ErrorsPart2ofTrial3"=>$VM_4on8_ErrorsPart2ofTrial3,
"VM_4on8_ErrorsPart2ofABA"=>$VM_4on8_ErrorsPart2ofABA,
"VM_4on8_LatencyofABA"=>$VM_4on8_LatencyofABA,
"VM_4on8_AbsoluteProbeErrors"=>$VM_4on8_AbsoluteProbeErrors,
"VM_4on8_RotationalProbeErrors"=>$VM_4on8_RotationalProbeErrors,
"VM_4on8_TotalErrors"=>$VM_4on8_TotalErrors,
"VM_4on8_AverageErrors"=>$VM_4on8_AverageErrors,
"VM_4on8_AveragePart2ErrorsNoProbe"=>$VM_4on8_AveragePart2ErrorsNoProbe,
"VM_4on8_AveragePart2ErrorsAtrials"=>$VM_4on8_AveragePart2ErrorsAtrials,
"VM_4on8_ErrorsAminusB"=>$VM_4on8_ErrorsAminusB,
"VM_4on8_TotalLatency"=>$VM_4on8_TotalLatency,
"VM_4on8_AverageLatency"=>$VM_4on8_AverageLatency,
/*
"VM_4on8_AverageLatencyPart2"=>$VM_4on8_AverageLatencyPart2,
"VM_4on8_2GroupsStrategy"=>$VM_4on8_2GroupsStrategy,
"VM_4on8_4GroupsStrategy"=>$VM_4on8_4GroupsStrategy,
*/
		);


$mappings2 = array(
"CommentID"=>$commentID2,
"PSCID"=>$PSCID,
"Date_taken"=>$Session2_DateofTesting,
//"Session2_PersonTested"=>$Session2_PersonTested,
"WF_MeanPctDistanceError"=>$WF_MeanPctDistanceError,
"WF_MeanPctTimeError"=>$WF_MeanPctTimeError,
"WF_MeanPctAccuracy"=>$WF_MeanPctAccuracy,
"WF_MeanPctAccuracy3xIdealTime"=>$WF_MeanPctAccuracy3xIdealTime,
/*"GoNoGo_TTC"=>$GoNoGo_TTC,
"GoNoGo_CriteriaReached"=>$GoNoGo_CriteriaReached,
"GoNoGo_Probe1_PctCorrect"=>$GoNoGo_Probe1_PctCorrect,
"GoNoGo_Probe1and2_PctCorrect"=>$GoNoGo_Probe1and2_PctCorrect,
"GoNoGo_AllOpen1_PctCorrect"=>$GoNoGo_AllOpen1_PctCorrect,
"GoNoGo_AllOpen2_PctCorrect"=>$GoNoGo_AllOpen2_PctCorrect,
"Pairs_TTC"=>$Pairs_TTC,
"Pairs_TrialsAdministered"=>$Pairs_TrialsAdministered,
"Pairs_CriteriaReached"=>$Pairs_CriteriaReached,
"Pairs_AverageLatency"=>$Pairs_AverageLatency,
"Pairs_Probe1_PctCorrect"=>$Pairs_Probe1_PctCorrect,
"Pairs_Probe1and2_PctCorrect"=>$Pairs_Probe1and2_PctCorrect,
"Pairs_AllOpen1_PctCorrect"=>$Pairs_AllOpen1_PctCorrect,
*/
"ROCopy"=>$ROCopy,
"ROImmediateRecall"=>$ROImmediateRecall,
"RODelayRecall"=>$RODelayRecall,
"RAVLTTotalRecall"=>$RAVLTTotalRecall,
"RAVLTListBRecall"=>$RAVLTListBRecall,
"RAVLTInterferenceRecall"=>$RAVLTInterferenceRecall,
"RAVLT30MinuteDelayRecall"=>$RAVLT30MinuteDelayRecall,
"RAVLTRecognitionListA"=>$RAVLTRecognitionListA,
"RAVLTRecognitionListB"=>$RAVLTRecognitionListB,
"RAVLTFalsePositives"=>$RAVLTFalsePositives,
"RAVLTMisattributions"=>$RAVLTMisattributions,
"PerceivedStressScore"=>$PerceivedStressScore,
"SantaBarbaraSenseofDirectionScaleScore"=>$SantaBarbaraSenseofDirectionScaleScore,
"QualityofLifeScore"=>$QualityofLifeScore,
"SelfEsteemQuestionnaireScore"=>$SelfEsteemQuestionnaireScore,
"BarrattImpulsivityScaleScore"=>$BarrattImpulsivityScaleScore,

);

			$insertData1 = $mappings1;
			$insertData2 = $mappings2;

print_r($insertData1);
print_r($insertData2);

//insert into nav table 1
if (!$db->pselect("SELECT CommentID from navigational_task_session_1 where CommentID =:cid",array("cid"=>$commentID1))) {
			if (!empty($commentID1) ) {
				$success = $DB->insert($table1, $insertData1);
				if (Utility::isErrorX($success)) {
					fwrite(STDERR, "Failed to update navigational table, DB Error: " . $success->getMessage()."\n");
					return false;
				}
				echo "insert - Updated!\n";
			}
			else {
				echo "insert - Does not exist\n";

			}
} else {
                        if (!empty($commentID1) ) {
                              $success = $DB->update($table1, $insertData1, array('CommentID'=>$commentID1));
                                if (Utility::isErrorX($success)) {
                                        fwrite(STDERR, "Failed to update navigational table, DB Error: " . $success->getMessage()."\n");
                                        return false;
                                }
                              echo "update - Updated!\n";
                        }
                        else {
                                echo "update - Does not exist\n";

                        }
}

//insert into nav table 2
if (!$db->pselect("SELECT CommentID from navigational_task_session_2 where CommentID =:cid",array("cid"=>$commentID2))) {
			if (!empty($commentID2) ) {
				$success = $DB->insert($table2, $insertData2);
				if (Utility::isErrorX($success)) {
					fwrite(STDERR, "Failed to update navigational table, DB Error: " . $success->getMessage()."\n");
					return false;
				}
				echo "insert - Updated!\n";
			}
			else {
				echo "insert - Does not exist\n";

			}
} else {
                        if (!empty($commentID2) ) {
                              $success = $DB->update($table2, $insertData2,array('CommentID'=>$commentID2));
                                if (Utility::isErrorX($success)) {
                                        fwrite(STDERR, "Failed to update navigational table, DB Error: " . $success->getMessage()."\n");
                                        return false;
                                }
                              echo "update - Updated!\n";
                        }
                        else {
                                echo "update - Does not exist\n";

                        }
}
		//}


//die();

	}
}

?>

