<?php 
require_once "generic_includes.php";
require_once "PEAR.php";

$fixedLines = file("/tmp/PATIENTS-JTM-DM-CM.csv");
$thisField = array();
$table = 'participant_status';
$table_history = 'participant_status_history';

$counter = 0;
$candID = "";
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
			if ($key == 'Alloc') {
				$counter ++;
				$PSCID = $thisField[$key];
                                switch (strlen($PSCID)) {
                                    case 1:
                                        $PSCID = "MTL000" . $PSCID;
                                        break;
                                    case 2:
                                        $PSCID = "MTL00" . $PSCID;
                                        break;
                                    case 3:
                                        $PSCID = "MTL0" . $PSCID;
                                        break;
                                    default:
                                        echo "ERROR parsing PSCID!";
                                        die();
                                }
				echo "-------count: " . $counter ."\n";
				echo "-------PSCID: " . $PSCID . "\n";
			}

			if ($key == 'ITT') {
				$ITT = $thisField[$key];
			}

			if (trim($key) == 'mITT') {
				$mITT = trim($thisField[$key]);
			}

                }

		$config =& NDB_Config::singleton();
		$db =& Database::singleton();

                $today = date("Y-m-d");
                $setData = array('naproxen_ITT'=>$ITT, 'naproxen_mITT'=>$mITT, 'entry_staff'=>"justin", 'data_changed_date'=>$today);
		$candID = $db->pselectOne("SELECT CandID from candidate where PSCID =:pid", array("pid"=>$PSCID));
		$where = array('CandID'=>$candID);

		if (!empty($candID)) {
                        $exists = $db->pselectOne("SELECT 'X' FROM participant_status WHERE CandID =:cid", array("cid"=>$candID));
			if (!empty($exists)) {
	 			$success = $db->update($table, $setData, $where);
				echo "Updated!\n";
			} else {
				echo "Missing CandID $candID in Participant Status table!\n";
			}
		} else {
			echo "Error updating Participant Status.\n";
		}

                $currentData = $db->pselectRow("SELECT * FROM participant_status WHERE CandID =:cid", array("cid"=>$candID));
                $currentData['naproxen_ITT'] = $ITT;
                $currentData['naproxen_mITT'] = $mITT;
                $currentData['entry_staff'] = "justin";
                $currentData['ID'] = "";
                $currentData['data_changed_date'] = $today;

                if (count($currentData) > 2) {
                        $success = $db->insert($table_history, $currentData);
                        echo "Updated!\n";
                } else {
                        echo "Error updating Participant Status History.\n";
                }
	}
}

?>
