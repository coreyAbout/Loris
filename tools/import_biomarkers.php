<?php 
require_once "generic_includes.php";
require_once "PEAR.php";


//exec("perl cleanup.pl EARLI-DATA-AOSI-1328311043.csv >/tmp/cleanup_xyz");
$fixedLines = file("/tmp/csf.csv");
$fields = array();
$thisField = array();
$table = 'LP'; //$argv[1];

$mappings = array(
		"Tau"=>"tau",
		"b-amyloid"=>"b_amyloid",
		"pTau"=>"ptau",
		);

$counter = 0;
$commentID = "";
$candID = "";
$PSCID = "";
$sessionID = "";
$visit = "";


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


			if ($key == 'SubjectID') {
				$counter ++;
				$PSCID = $thisField[$key];
				echo "-------count: " . $counter ."\n";
				echo "-------PSCID: " . $PSCID . "\n";
			}

			if ($key == 'visit')
			{
				if ($thisField[$key] == 'F0')
					$visit = 'NAPLP00';
				elseif($thisField[$key] == 'F3')
					$visit = 'NAPLP03';
                                elseif($thisField[$key] == 'F12')
                                        $visit = 'NAPLP12';
                                elseif($thisField[$key] == 'F24')
                                        $visit = 'NAPLP24';
                                elseif($thisField[$key] == 'F36')
                                        $visit = 'NAPLP36';
                                elseif($thisField[$key] == 'F48')
                                        $visit = 'NAPLP48';

				echo "Visit: " . $visit . "\n";
			}

			if ($key == 'total_TAU') {
				$ELISA_tau = $thisField[$key];
			}

			if ($key == 'b-amyloid') {
				$ELISA_b_amyloid = $thisField[$key];
			}

			if ($key == 'phospho_TAU') {
				$ELISA_ptau = $thisField[$key];
			}

                        if (strpos($key, 'ELISA_date') !== FALSE) {
                           if ($thisField[$key] != '' && $thisField[$key] != "\r\n") {
                                $parsedate = date("Y-m-d",strtotime($thisField[$key]));
                                $ELISA_date = $parsedate;
                           }  else {
                                $ELISA_date = NULL;
                           }
                        }

                        if ($key == 'NFL') {
                                $ELISA_NFL = $thisField[$key];
                        }
/*
                        if ($key == 'ApoAI') {
                                $sixplex_ApoAI = $thisField[$key];
                        }

                        if ($key == 'ApoAII') {
                                $sixplex_ApoAII = $thisField[$key];
                        }

                        if ($key == 'ApoB') {
                                $sixplex_ApoB = $thisField[$key];
                        }

                        if ($key == 'ApoCII') {
                                $sixplex_ApoCII = $thisField[$key];
                        }

                        if ($key == 'ApoCIII') {
                                $sixplex_ApoCIII = $thisField[$key];
                        }
*/
                        if ($key == 'ApoE') {
                                $sixplex_ApoE = $thisField[$key];
                        }
/*
                        if (strpos($key, '6plex_date') !== FALSE) {
                           if ($thisField[$key] != '' && $thisField[$key] != "\r\n") {
                                $parsedate = date("Y-m-d",strtotime($thisField[$key]));
                                $sixplex_date = $parsedate;
                           }  else {
                                $sixplex_date = NULL;
                           }
                        }
*/

                        if ($key == 'EGF') {
                                $EGF = $thisField[$key];
                        }

                        if ($key == 'Eotaxin') {
                                $Eotaxin = $thisField[$key];
                        }

                        if ($key == 'G-CSF') {
                                $G_CSF = $thisField[$key];
                        }

                        if ($key == 'GM-CSF') {
                                $GM_CSF = $thisField[$key];
                        }

                        if ($key == 'IFN_alpha_2') {
                                $IFN_alpha_2 = $thisField[$key];
                        }

                        if ($key == 'IFNy') {
                                $IFNy = $thisField[$key];
                        }

                        if ($key == 'IL-10') {
                                $IL_10 = $thisField[$key];
                        }

                        if ($key == 'IL-12P40') {
                                $IL_12P40 = $thisField[$key];
                        }

                        if ($key == 'IL-12P70') {
                                $IL_12P70 = $thisField[$key];
                        }

                        if ($key == 'IL-13') {
                                $IL_13 = $thisField[$key];
                        }

                        if ($key == 'IL-15') {
                                $IL_15 = $thisField[$key];
                        }

                        if ($key == 'IL-17') {
                                $IL_17 = $thisField[$key];
                        }

                        if ($key == 'IL-1RA') {
                                $IL_1RA = $thisField[$key];
                        }

                        if ($key == 'IL-1alpha') {
                                $IL_1alpha = $thisField[$key];
                        }

                        if ($key == 'IL-1beta') {
                                $IL_1beta = $thisField[$key];
                        }

                        if ($key == 'IL-2') {
                                $IL_2 = $thisField[$key];
                        }

                        if ($key == 'IL-3') {
                                $IL_3 = $thisField[$key];
                        }

                        if ($key == 'IL-4') {
                                $IL_4 = $thisField[$key];
                        }

                        if ($key == 'IL-5') {
                                $IL_5 = $thisField[$key];
                        }

                        if ($key == 'IL-6') {
                                $IL_6 = $thisField[$key];
                        }

                        if ($key == 'IL-7') {
                                $IL_7 = $thisField[$key];
                        }

                        if ($key == 'IL-8') {
                                $IL_8 = $thisField[$key];
                        }

                        if ($key == 'IP-10') {
                                $IP_10 = $thisField[$key];
                        }

                        if ($key == 'MCP-1') {
                                $MCP_1 = $thisField[$key];
                        }

                        if ($key == 'MIP-1alpha') {
                                $MIP_1alpha = $thisField[$key];
                        }

                        if ($key == 'MIP-1beta') {
                                $MIP_1beta = $thisField[$key];
                        }

                        if ($key == 'TNF_alpha') {
                                $TNF_alpha = $thisField[$key];
                        }

                        if ($key == 'TNF_beta') {
                                $TNF_beta = $thisField[$key];
                        }

                        if ($key == 'VEGF') {
                                $VEGF = $thisField[$key];
                        }

                        if ($key == '29-plex_date') {
                                $29_plex_date = $thisField[$key];
                        }

                        if ($key == 'Abeta34') {
                                $Abeta34 = $thisField[$key];
                        }

                        if ($key == 'Abeta34_date') {
                                $Abeta34_date = $thisField[$key];
                        }

                        if ($key == 'Abeta40') {
                                $Abeta40 = $thisField[$key];
                        }

                        if ($key == 'synuclein') {
                                $synuclein = $thisField[$key];
                        }

                        if ($key == 'date_synuclein') {
                                $date_synuclein = $thisField[$key];
                        }

                }




			$config =& NDB_Config::singleton();
			$db =& Database::singleton();
			if(Utility::isErrorX($db)) {
				fwrite(STDERR, "Could not connect to database: ".$db->getMessage());
				return false;
			}

			$candID = $db->pselectOne("SELECT CandID from candidate where PSCID =:pid", array("pid"=>$PSCID)); 
			$sessionID = $db->pselectOne("SELECT ID from session where CandID =:cid and Visit_label =:visit", array('cid'=>$candID, 'visit'=>$visit));
                        if (empty($sessionID)) {
                                $visit = str_replace("NAP", "PRE", $visit);
                                $sessionID = $db->pselectOne("SELECT ID from session where CandID =:cid and Visit_label =:visit", array('cid'=>$candID, 'visit'=>$visit));
                        }

			if (empty($sessionID)) {
				echo "Error: Failed to update for CandID " . $candID . " for Visit " . $visit . "\n";
			}
			$commentID = $db->pselectOne("SELECT CommentID from flag where sessionID=:sid and test_name = 'LP' and CommentID not like 'DDE%'", array('sid'=>$sessionID));
			$DDE_commentID = $db->pselectOne("SELECT CommentID from flag where sessionID=:sid and test_name = 'LP' and CommentID like 'DDE%'", array('sid'=>$sessionID));
			print "CommentID: " . $commentID ."\n";

			$insertData = array('ELISA_tau'=>$ELISA_tau, 'ELISA_b_amyloid'=>$ELISA_b_amyloid, 'ELISA_ptau'=>$ELISA_ptau, 'ELISA_date'=>$ELISA_date, 'ELISA_NFL'=>$ELISA_NFL, /*'6plex_ApoAI'=>$sixplex_ApoAI, '6plex_ApoAII'=>$sixplex_ApoAII, '6plex_ApoB'=>$sixplex_ApoB, '6plex_ApoCII'=>$sixplex_ApoCII, '6plex_ApoCIII'=>$sixplex_ApoCIII,*/ '6plex_ApoE'=>$sixplex_ApoE, /*'6plex_date'=>$sixplex_date*/, 'EGF'=>$EGF, 'Eotaxin'=>$Eotaxin, 'G_CSF'=>$G_CSF, 'GM_CSF'=>$GM_CSF, 'IFN_alpha_2'=>$IFN_alpha_2, 'IFNy'=>$IFNy, 'IL_10'=>$IL_10, 'IL_12P40'=>$IL_12P40, 'IL_12P70'=>$IL_12P70, 'IL_13'=>$IL_13, 'IL_15'=>$IL_15, 'IL_17'=>$IL_17, 'IL_1RA'=>$IL_1RA, 'IL_1alpha'=>$IL_1alpha, 'IL_1beta'=>$IL_1beta, 'IL_2'=>$IL_2, 'IL_3'=>$IL_3, 'IL_4'=>$IL_4, 'IL_5'=>$IL_5, 'IL_6'=>$IL_6, 'IL_7'=>$IL_7, 'IL_8'=>$IL_8, 'IP_10'=>$IP_10, 'MCP_1'=>$MCP_1, 'MIP_1alpha'=>$MIP_1alpha, 'MIP_1beta'=>$MIP_1beta, 'TNF_alpha'=>$TNF_alpha, 'TNF_beta'=>$TNF_beta, 'VEGF'=>$VEGF, '29_plex_date'=>$29_plex_date, 'Abeta34'=>$Abeta34, 'Abeta34_date'=>$Abeta34_date, 'Abeta40'=>$Abeta40, 'synuclein'=>$synuclein, 'date_synuclein'=>$date_synuclein);

			$where = array('CommentID'=>$commentID);
			$where2 = array('CommentID'=>$DDE_commentID);

			//populate fields in LP form
			if (!empty($sessionID) && !empty($commentID)) {
				$success = $DB->update($table, $insertData, $where);
				if (Utility::isErrorX($success)) {
					fwrite(STDERR, "Failed to update LP table, DB Error: " . $success->getMessage()."\n");
					return false;
				}
				echo "Updated!\n";
			}
			else {
				echo "Does not exist\n";

			}
			
			//populate DDE fields in LP form
			if (!empty($sessionID) && !empty($DDE_commentID)) {
				$success2 = $DB->update($table, $insertData, $where2);
				if (Utility::isErrorX($success2)) {
					fwrite(STDERR, "Failed to update LP table, DB Error: " . $success2->getMessage()."\n");
					return false;
				}
				echo "DDE Updated!\n";
			}
			else {
				echo "DDE Does not exist\n";
			}



	}
}

?>
