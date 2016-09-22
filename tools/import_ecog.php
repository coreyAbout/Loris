<?php

/*
  Import script for ECog instrument
*/

require_once "generic_includes.php";
require_once "PEAR.php";

$fixedLines = file("/tmp/ecog.csv");
$fields = array();
$thisField = array();
$table = 'ecog';

$counter = 0;
$id = 0;
$PSCID = "";

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
                $insert_data = array();

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

                        $key = str_replace("\r\n", "", $key);
                        $value = str_replace("\r\n", "", $value);

			if ($key == 'PSCID') {
                                $counter ++;
                                echo "-------count: " . $counter ."\n";
				$PSCID = $thisField[$key];
				echo "-------PSCID: " . $PSCID . "\n";
			}

                        if ($value == 'Yes' || $value == 'Oui') {
                                $value = 'yes';
                        }
                        if ($value == 'No' || $value == 'Non') {
                                $value = 'no';
                        }
                        if ($value == 'Not Answered') {
                                $value = 'not_answered';
                        }
                        if ($value == '') {
                                $value = null;
                        }

                        if ($value == 'Yes but I am not worried about it' || $value == 'Oui mais ça ne me préoccupe pas') {
                                $value = 'yes_no_worried';
                        }
                        if ($value == 'Yes and I am worried about it (SCI)' || $value == 'Oui et ça me préoccupe (SCI)') {
                                $value = 'yes_worried';
                        }
                        if ($value == 'The history/antecedent of Alzheimer in your family' || $value == "vous avez un antécédent familial de maladie d'Alzheimer") {
                                $value = 'alzheimer_family';
                        }
                        if ($value == 'You think the changes in your memory are abnormal' || $value == 'vous éprouvez un changement au niveau de la mémoire que vous considéré comme anormal') {
                                $value = 'changes_abnormal';
                        }
                        if ($value == 'a and b' || $value == 'a et b') {
                                $value = 'both';
                        }
                        if ($value == 'Better' || $value == 'Meilleure') {
                                $value = 'better';
                        }
                        if ($value == 'About the same' || $value == 'Similaire') {
                                $value = 'about_same';
                        }
                        if ($value == 'A bit worse' || $value == 'Un peu moins bonne') {
                                $value = 'bit_worse';
                        }
                        if ($value == 'Much worse' || $value == 'Beaucoup moins bonne') {
                                $value = 'much_worse';
                        }
                        if ($value == 'Better or no change' || $value == 'Meilleure ou identique') {
                                $value = 'better_no_change';
                        }
                        if ($value == 'Occasionnally worse' || $value == "Questionnable/moins bonne à l'occasion") {
                                $value = 'occassionally_worse';
                        }
                        if ($value == 'Consistently a little worse' || $value == 'Toujours un peu moins bonne') {
                                $value = 'consistently_little_worse';
                        }
                        if ($value == 'Consistently much worse' || $value == 'Toujours vraiment moins bonne') {
                                $value = 'consistently_much_worse';
                        }
                        if ($value == "I don't know" || $value == 'Je ne sais pas') {
                                $value = 'dont_know';
                        }

                        $insert_data[$key] = $value;

		}//closing inner most foreach

		$config =& NDB_Config::singleton();
		$db =& Database::singleton();
		if(Utility::isErrorX($db)) {
			fwrite(STDERR, "Could not connect to database: ".$db->getMessage());
			return false;
		}

                //insert session table
                $candID = $db->pselectOne("SELECT CandID FROM candidate WHERE PSCID=:pscid", array("pscid"=>$PSCID));
                $SubprojectID = $db->pselectOne("SELECT SubprojectID FROM session WHERE CandID=$candID AND Date_visit<=':date' ORDER BY ID desc", array('date'=>$insert_data['Date_taken']));
                $query = "SELECT c.PSCID, c.CenterID, IFNULL(max(s.VisitNo)+1, 1) AS nextVisitNo FROM candidate AS c LEFT OUTER JOIN session AS s USING (CandID) WHERE c.CandID=:CaID AND (s.Active='Y' OR s.Active is null) GROUP BY c.CandID";
                $row = $db->pselectRow($query, array('CaID' => $candID));
                if ($SubprojectID == 1) {
                    $VisitLabel = 'PREEC00';
                } elseif ($SubprojectID == 2) {
                    $VisitLabel = 'NAPEC00';
                } elseif ($SubprojectID == 3) {
                    $VisitLabel = 'POIEC00';
                }

                $today = date("Y-m-d");

                $sessionData = array(
                       'CandID'          => $candID,
                       'SubprojectID'    => $SubprojectID,
                       'VisitNo'         => $row['nextVisitNo'],
                       'Visit_label'     => $VisitLabel,
                       'CenterID'        => $row['CenterID'],
                       'Current_stage'   => 'Approval',
                       'Submitted'       => 'Y',
                       'Scan_done'       => 'N',
                       'Visit'           => 'Pass',
                       'registeredBy'    => 'justin',
                       'UserID'          => 'justin',
                       'Date_registered' => $today,
                       'Date_active'     => $today,
                       'Date_visit'      => $insert_data['Date_taken'],
                );

                print_r($sessionData);
                $success = $db->insert('session', $sessionData);

                //insert flag table & test names table
                $newSID = $db->pselectOne("SELECT ID FROM session WHERE CandID=:cid ORDER BY ID DESC", array("cid"=>$candID));
                $testName = $table;
                $testID = $db->pselectOne("SELECT ID FROM test_names WHERE Test_name=:TN", array('TN' => $testName));
                $commentID = $candID . $PSCID . $newSID . $SubprojectID . $testID . time();

                // insert into the test table
                $success = $db->insert($testName, array('CommentID' => $commentID));
                // insert into the flag table
                $success = $db->insert(
                            'flag',
                            array(
                             'SessionID' => $newSID,
                             'Test_name' => $testName,
                             'CommentID' => $commentID,
                             'UserID'    => 'justin',
                             'Testdate'  => null,
                            )
                           );
                // generate the double data entry commentid
                $ddeCommentID = 'DDE_'.$commentID;
                // insert the dde into the test table
                $success = $db->insert($testName, array('CommentID' => $ddeCommentID));
                // insert the dde into the flag table
                $success = $db->insert(
                            'flag',
                            array(
                             'SessionID' => $newSID,
                             'Test_name' => $testName,
                             'CommentID' => $ddeCommentID,
                             'UserID'    => 'justin',
                             'Testdate'  => null,
                            )
                           );

                $insert_data['Data_entry_completion_status'] = 'Complete';

                $examinerID = $db->pselectOne("SELECT examinerID FROM examiners WHERE full_name=:fname", array('fname'=>$insert_data['Examiner']));
                $insert_data['Examiner'] = $examinerID;

                $dob = $db->pselectOne("SELECT DoB FROM candidate WHERE PSCID=$PSCID", array());
                $age = Utility::calculateAge($dob, $insert_data['Date_taken']);
                $agemonths = $age[0]*12 + $age[1] + ($age[2]/30);
                // 1 Decimal.
                $agemonths = (round($agemonths*10) / 10.0);
                $insert_data['Candidate_Age'] = $agemonths;

                if ($insert_data['test_language'] == 'French') {
                    $insert_data['test_language'] = 'french';
                } elseif ($insert_data['test_language'] == 'English') {
                    $insert_data['test_language'] = 'english';
                }

                //update ecog table
                $likecommentID = '%' . $commentID . '%';
		if ($db->pselectOne("SELECT count(*) FROM ecog WHERE CommentID LIKE '{$likecommentID}'", array()) == 2) {
			$success = $db->update($table, $insert_data, array("CommentID"=>$commentID));
			if (Utility::isErrorX($success)) {
				fwrite(STDERR, "Failed to update ecog table, DB Error: " . $success->getMessage()."\n");
				return false;
			}
                        $success = $db->update($table, $insert_data, array("CommentID"=>$ddeCommentID));
                        if (Utility::isErrorX($success)) {
                                fwrite(STDERR, "Failed to update ecog table DDE, DB Error: " . $success->getMessage()."\n");
                                return false;
                        }
			echo "Updated " . $PSCID . "!\n";
		} else {
			echo "Failure " . $PSCID . "\n";
		}

	} //closing else
} //outer most for

?>
