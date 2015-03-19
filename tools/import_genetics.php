<?php
require_once "generic_includes.php";
require_once "PEAR.php";

$fixedLines = file("/tmp/genetics.csv");
$fields = array();
$thisField = array();
$table = 'genetics';

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
			if ($key == 'Subject_ID') {
                                $counter ++;
                                echo "-------count: " . $counter ."\n";
				$PSCID = $thisField[$key];
				echo "-------PSCID: " . $PSCID . "\n";
			}
			elseif ($key == 'ApoE') {
			$ApoE = $thisField[$key];
			}
			elseif ($key == 'ApoE_112') {
			$ApoE_112 = $thisField[$key];
			}
			elseif ($key == 'ApoE_158') {
			$ApoE_158 = $thisField[$key];
			}
			elseif ($key == 'apoE_allele_no') {
			$apoE_allele_no = $thisField[$key];
			}
			elseif ($key == 'E4_allele_no') {
			$E4_allele_Bin = $thisField[$key];
			}
			elseif ($key == 'Technicien_ApoE') {
			$Technicien_ApoE = $thisField[$key];
			}
			elseif ($key == 'Method_ApoE') {
			$Method_ApoE = $thisField[$key];
			}
			elseif ($key == 'Reference_ApoE') {
                           if ($thisField[$key] != '') {
				$parsedate = date("Y-m-d",strtotime($thisField[$key]));
				$Reference_ApoE = $parsedate;
			   }  else {
                                $Reference_ApoE = NULL;
                           }
			}
			elseif ($key == 'BchE_K_variant') {
			$BchE_K_variant = $thisField[$key];
			}
			elseif ($key == 'K_variant_copie_no') {
			$K_variant_copie_no = $thisField[$key];
			}
			elseif ($key == 'K_variant') {
			$K_variant_bin = $thisField[$key];
			}
			elseif ($key == 'Technicien_BchE') {
			$Technicien_BchE = $thisField[$key];
			}
			elseif ($key == 'Method_BchE') {
			$Method_BchE = $thisField[$key];
			}
			elseif ($key == 'Reference_BchE') {
                           if ($thisField[$key] != '') {
				$parsedate = date("Y-m-d",strtotime($thisField[$key]));
				$Reference_BchE = $parsedate;
			   } else {
                                $Reference_BchE = NULL;
                           }
			}
			elseif ($key == 'BDNF') {
			$BDNF = $thisField[$key];
			}
			elseif ($key == 'BDNF_allele_no') {
			$BDNF_allele_no = $thisField[$key];
			}
			elseif ($key == 'BDNF_copie_no') {
			$BDNF_copie_bin = $thisField[$key];
			}
			elseif ($key == 'Technicien_BDNF') {
			$Technicien_BDNF = $thisField[$key];
			}
			elseif ($key == 'Method_BDNF') {
			$Method_BDNF = $thisField[$key];
			}
			elseif ($key == 'Reference_BDNF') {
			   if ($thisField[$key] != '') {
				$parsedate = date("Y-m-d",strtotime($thisField[$key]));
				$Reference_BDNF = $parsedate;
			   } else {
                                $Reference_BDNF = NULL;
                           }
			}
			elseif ($key == 'HMGR_Intron_M') {
			$HMGR_Intron_M = $thisField[$key];
			}
			elseif ($key == 'Intron_M_allele_no') {
			$Intron_M_allele_no = $thisField[$key];
			}
			elseif ($key == 'Technicien_M') {
			$Technicien = $thisField[$key];
			}
			elseif ($key == 'Method_M') {
			$Method = $thisField[$key];
			}
                        elseif ($key == 'Reference_M') {
                           if ($thisField[$key] != '') {
                                $parsedate = date("Y-m-d",strtotime($thisField[$key]));
                                $Reference_M = $parsedate;
                           } else {
                                $Reference_M = NULL;
                           }
                        }
                        elseif ($key == 'TLR4_rs_4986790') {
                        $TLR4_rs_4986790 = $thisField[$key];
                        }
                        elseif ($key == 'TLR4_allele_no') {
                        $TLR4_allele_no = $thisField[$key];
                        }
                        elseif ($key == 'Technicien_TLR4') {
                        $Technicien_TLR4 = $thisField[$key];
                        }
                        elseif ($key == 'Method_TLR4') {
                        $Method_TLR4 = $thisField[$key];
                        }
                        elseif ($key == 'Reference_TLR4') {
                           if ($thisField[$key] != '') {
                                $parsedate = date("Y-m-d",strtotime($thisField[$key]));
                                $Reference_TLR4 = $parsedate;
                           } else {
                                $Reference_TLR4 = NULL;
                           }
                        }
                        elseif ($key == 'PPP2r1Ars10406151') {
                        $PPP2r1A_rs_10406151 = $thisField[$key];
                        }
                        elseif ($key == 'ppp2r1A_allele_no') {
                        $ppp2r1A_allele_no = $thisField[$key];
                        }
                        elseif ($key == 'ppp2r1A_copie_no') {
                        $ppp2r1A_copie_no = $thisField[$key];
                        }
                        elseif ($key == 'Technicien_ppp2r1a') {
                        $Technicien_ppp2r1a = $thisField[$key];
                        }
                        elseif ($key == 'Method_ppp2r1a') {
                        $Method_ppp2r1a = $thisField[$key];
                        }
			elseif ($key == 'reference_ppp2r1a') {
                           if ($thisField[$key] != '') {
				$parsedate = date("Y-m-d",strtotime($thisField[$key]));
				$Reference_ppp2r1a = $parsedate;
			   } else {
                                $Reference_ppp2r1a = NULL;
                           }
                        }
                        elseif ($key == 'Comments') {
                        $comments = $thisField[$key];
                        }
                        else {
                        $is_destroyed = $thisField[$key];
			}

		}//closing last foreach


		$config =& NDB_Config::singleton();
		$db =& Database::singleton();
		if(Utility::isErrorX($db)) {
			fwrite(STDERR, "Could not connect to database: ".$db->getMessage());
			return false;
		}

		$mappings1 = array(
				"PSCID"=>$PSCID,
				"ApoE"=>$ApoE,
				"ApoE_112"=>$ApoE_112,
				"ApoE_158"=>$ApoE_158,
				"apoE_allele_no"=>$apoE_allele_no,
				"E4_allele_Bin"=>$E4_allele_Bin,
				"Technicien_ApoE"=>$Technicien_ApoE,
				"Method_ApoE"=>$Method_ApoE,
				"Reference_ApoE"=>$Reference_ApoE,
				"BchE_K_variant"=>$BchE_K_variant,
				"K_variant_copie_no"=>$K_variant_copie_no,
				"K_variant_bin"=>$K_variant_bin,
				"Technicien_BchE"=>$Technicien_BchE,
				"Method_BchE"=>$Method_BchE,
				"Reference_BchE"=>$Reference_BchE,
				"BDNF"=>$BDNF,
				"BDNF_allele_no"=>$BDNF_allele_no,
				"BDNF_copie_bin"=>$BDNF_copie_bin,
				"Technicien_BDNF"=>$Technicien_BDNF,
				"Method_BDNF"=>$Method_BDNF,
				"Reference_BDNF"=>$Reference_BDNF,
				"HMGR_Intron_M"=>$HMGR_Intron_M,
				"Intron_M_allele_no"=>$Intron_M_allele_no,
				"Technicien_M"=>$Technicien,
				"Method_M"=>$Method,
				"Reference_M"=>$Reference_M,
				"TLR4_rs_4986790"=>$TLR4_rs_4986790,
				"TLR4_allele_no"=>$TLR4_allele_no,
				"Technicien_TLR4"=>$Technicien_TLR4,
				"Method_TLR4"=>$Method_TLR4,
				"Reference_TLR4"=>$Reference_TLR4,
				"PPP2r1A_rs_10406151"=>$PPP2r1A_rs_10406151,
				"ppp2r1A_allele_no"=>$ppp2r1A_allele_no,
				"ppp2r1A_copie_no"=>$ppp2r1A_copie_no,
				"Technicien_ppp2r1a"=>$Technicien_ppp2r1a,
				"Method_ppp2r1a"=>$Method_ppp2r1a,
				"Reference_ppp2r1a"=>$Reference_ppp2r1a,
				"comments"=>$comments,
				"is_destroyed"=>$is_destroyed,
			);

		$insertData1 = $mappings1;
		print_r ($insertData1);

		//insert/update into genetics table
		if ($db->selectOne("SELECT count(*) FROM genetics WHERE PSCID='$PSCID'") > 0) {
			$success = $DB->update($table, $insertData1,array("PSCID"=>$PSCID));
			if (Utility::isErrorX($success)) {
				fwrite(STDERR, "Failed to update genetics table, DB Error: " . $success->getMessage()."\n");
				return false;
			}
			echo "Updated!\n";
		} else {
			$success = $DB->insert($table, $insertData1);
			if (Utility::isErrorX($success)) {
				fwrite(STDERR, "Failed to insert genetics table, DB Error: " . $success->getMessage()."\n");
				return false;
			}
			echo "New PSCID " . $PSCID . "@\n";
		}
	}
}

?>
