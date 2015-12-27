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
                        elseif ($key == 'Technicien_ppp2r1a') {
                        $Technicien_ppp2r1a = $thisField[$key];
                        }
                        elseif ($key == 'Method_ppp2r1a') {
                        $Method_ppp2r1a = $thisField[$key];
                        }
			elseif ($key == 'Reference_PPP2R1A') {
                           if ($thisField[$key] != '') {
				$parsedate = date("Y-m-d",strtotime($thisField[$key]));
				$Reference_ppp2r1a = $parsedate;
			   } else {
                                $Reference_ppp2r1a = NULL;
                           }
                        }
                        elseif ($key == 'CDK5RAP2_rs10984186') {
                        $CDK5RAP2_rs10984186 = $thisField[$key];
                        }
                        elseif ($key == 'Technicien_CDK5RAP2') {
                        $Technicien_CDK5RAP2 = $thisField[$key];
                        }
                        elseif ($key == 'Method_CDK5RAP2') {
                        $Method_CDK5RAP2 = $thisField[$key];
                        }
                        elseif ($key == 'Reference_CDK5RAP2') {
                           if ($thisField[$key] != '') {
                                $parsedate = date("Y-m-d",strtotime($thisField[$key]));
                                $Reference_CDK5RAP2 = $parsedate;
                           } else {
                                $Reference_CDK5RAP2 = NULL;
                           }
                        }
                        else {
                        $comments = $thisField[$key];
                        }

		}//closing last foreach


		$config =& NDB_Config::singleton();
		$db =& Database::singleton();
		if(Utility::isErrorX($db)) {
			fwrite(STDERR, "Could not connect to database: ".$db->getMessage());
			return false;
		}


if ($ApoE == '4/4' || $ApoE == '4-4') {
 $apoE_allele_no = 2;
} elseif ($ApoE == '3-4' || $ApoE == '4-3' || $ApoE == '3/4' || $ApoE == '4/3' || $ApoE == '4/2' || $ApoE == '2/4' || $ApoE == '4-2' || $ApoE == '2-4') {
 $apoE_allele_no = 1;
} elseif ($ApoE == '3-3' || $ApoE == '2-3' || $ApoE == '3-2' || $ApoE == '2-2' || $ApoE == '3/3' || $ApoE == '2/3' || $ApoE == '3/2' || $ApoE == '2/2') {
 $apoE_allele_no = 0;
} else {
 $apoE_allele_no = null;
}

if ($apoE_allele_no === 0 || $ApoE == '2/4' || $ApoE == '4/2' || $ApoE == '2-4' || $ApoE == '4-2') {
 $E4_allele_Bin = 0;
} elseif ($apoE_allele_no > 0) {
 $E4_allele_Bin = 1;
} else {
 $E4_allele_Bin = null;
}

if ($BchE_K_variant == 'AA') {
 $K_variant_copie_no = 2;
} elseif ($BchE_K_variant == 'AG' || $BchE_K_variant == 'GA') {
 $K_variant_copie_no = 1;
} elseif ($BchE_K_variant == 'GG') {
 $K_variant_copie_no = 0;
} else {
 $K_variant_copie_no = null;
}

if ($K_variant_copie_no === 0) {
 $K_variant_bin = 0;
} elseif ($K_variant_copie_no > 0) {
 $K_variant_bin = 1;
} else {
 $K_variant_bin = null;
}

if ($BDNF == 'AA') {
 $BDNF_allele_no = 2;
} elseif ($BDNF == 'AG' || $BDNF == 'GA') {
 $BDNF_allele_no = 1;
} elseif ($BDNF == 'GG') {
 $BDNF_allele_no = 0;
} else {
 $BDNF_allele_no = null;
}

if ($BDNF_allele_no === 0) {
 $BDNF_copie_bin = 0;
} elseif ($BDNF_allele_no > 0) {
 $BDNF_copie_bin = 1;
} else {
 $BDNF_copie_bin = null;
}

if ($HMGR_Intron_M == 'TT') {
 $Intron_M_allele_no = 2;
} elseif ($HMGR_Intron_M == 'CT' || $HMGR_Intron_M == 'TC') {
 $Intron_M_allele_no = 1;
} elseif ($HMGR_Intron_M == 'CC') {
 $Intron_M_allele_no = 0;
} else {
 $Intron_M_allele_no = null;
}

if ($Intron_M_allele_no == 2) {
 $Intron_M_protective = 1;
} elseif ($Intron_M_allele_no < 2) {
 $Intron_M_protective = 0;
} else {
 $Intron_M_protective = null;
}

if ($TLR4_rs_4986790 == 'GG') {
 $TLR4_allele_no = 2;
} elseif ($TLR4_rs_4986790 == 'AG' || $TLR4_rs_4986790 == 'GA') {
 $TLR4_allele_no = 1;
} elseif ($TLR4_rs_4986790 == 'AA') {
 $TLR4_allele_no = 0;
} else {
 $TLR4_allele_no = null;
}

if ($PPP2r1A_rs_10406151 == 'TT') {
 $ppp2r1A_allele_no = 2;
} elseif ($PPP2r1A_rs_10406151 == 'TC' || $PPP2r1A_rs_10406151 == 'CT') {
 $ppp2r1A_allele_no = 1;
} elseif ($PPP2r1A_rs_10406151 == 'CC') {
 $ppp2r1A_allele_no = 0;
} else {
 $ppp2r1A_allele_no = null;
}

if ($ppp2r1A_allele_no === 1 || $ppp2r1A_allele_no === 0) {
 $ppp2r1A_copie_no = 0;
} elseif ($ppp2r1A_allele_no == 2) {
 $ppp2r1A_copie_no = 1;
} else {
 $ppp2r1A_copie_no = null;
}

if ($CDK5RAP2_rs10984186 == 'TT') {
 $CDK5RAP2_rs10984186_allele_no = 2;
} elseif ($CDK5RAP2_rs10984186 == 'TC' || $CDK5RAP2_rs10984186 == 'CT') {
 $CDK5RAP2_rs10984186_allele_no = 1;
} elseif ($CDK5RAP2_rs10984186 == 'CC') {
 $CDK5RAP2_rs10984186_allele_no = 0;
} else {
 $CDK5RAP2_rs10984186_allele_no = null;
}

if ($CDK5RAP2_rs10984186_allele_no === 0) {
 $CDK5RAP2_rs10984186_allele_bin = 0;
} elseif ($CDK5RAP2_rs10984186_allele_no > 0) {
 $CDK5RAP2_rs10984186_allele_bin = 1;
} else {
 $CDK5RAP2_rs10984186_allele_bin = null;
}

		$mappings1 = array(
				"PSCID"=>$PSCID,
				"ApoE"=>$ApoE,
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
                                "Intron_M_protective"=>$Intron_M_protective,
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
                                "CDK5RAP2_rs10984186"=>$CDK5RAP2_rs10984186,
                                "CDK5RAP2_rs10984186_allele_no"=>$CDK5RAP2_rs10984186_allele_no,
                                "CDK5RAP2_rs10984186_allele_bin"=>$CDK5RAP2_rs10984186_allele_bin,
                                "Technicien_CDK5RAP2"=>$Technicien_CDK5RAP2,
                                "Method_CDK5RAP2"=>$Method_CDK5RAP2,
                                "Reference_CDK5RAP2"=>$Reference_CDK5RAP2,
				"comments"=>$comments,
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
			echo "Updated " . $PSCID . "!\n";
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
