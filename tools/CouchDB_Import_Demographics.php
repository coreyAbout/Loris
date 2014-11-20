<?php
require_once 'generic_includes.php';
require_once 'CouchDB.class.inc';
require_once 'Database.class.inc';
class CouchDBDemographicsImporter {
    var $SQLDB; // reference to the database handler, store here instead
                // of using Database::singleton in case it's a mock.
    var $CouchDB; // reference to the CouchDB database handler

    // this is just in an instance variable to make
    // the code a little more readable.
    var $Dictionary = array(
        'CandID' => array(
            'Description' => 'DCC Candidate Identifier',
            'Type' => 'varchar(255)'
        ),  
        'PSCID' => array(
            'Description' => 'Project Candidate Identifier',
            'Type' => 'varchar(255)'
        ),  
        'Visit_label' => array(
            'Description' => 'Visit of Candidate',
            'Type' => 'varchar(255)'
        ),  
        'Cohort' => array(
            'Description' => 'Cohort of this session',
            'Type' => 'varchar(255)'
        ),  
        'Gender' => array(
            'Description' => 'Candidate\'s gender',
            'Type' => "enum('Male', 'Female')"
        ),
        'DoB' => array(
            'Description' => 'Candidate\'s date of birth',
            'Type' => "date"
        ),
        'Site' => array(
            'Description' => 'Site that this visit took place at',
            'Type' => "varchar(3)",
        ),  
        'Current_stage' => array(
            'Description' => 'Current stage of visit',
            'Type' => "enum('Not Started','Screening','Visit','Approval','Subject','Recycling Bin')"
        ),  
        'Visit' => array(
            'Description' => 'Visit status',
            'Type' => "enum('Pass','Failure','Withdrawal','In Progress')"
        ),
        'Failure' =>  array(
            'Description' => 'Whether Recycling Bin Candidate was failure or withdrawal',
            'Type' => "enum('Failure','Withdrawal','Neither')",
        ),
	'Status' => array(
	    'Description' => 'Participant status',
	    'Type' => "varchar(255)"
	),
	'reason_specify' => array(
	    'Description' => 'Participant status reason',
	    'Type' => "text"
	),
        'withdrawal_reasons' => array(
            'Description' => 'Participant status withdrawal reason',
            'Type' => "enum('1_voluntary_withdrawal','2_recommended_withdrawal','3_lost_follow_up','4_other')"
        ),
	'withdrawal_reasons_other_specify' => array(
	    'Description' => 'Other reason for withdrawal',
	    'Type' => "text"
	),
        'naproxen_eligibility' => array(
            'Description' => 'Naproxen Eligibility',
            'Type' => "enum('yes','no')"
        ),
        'naproxen_eligibility_status' => array(
            'Description' => 'Naproxen Eligibility Status',
            'Type' => "enum('active','stop_medication_active','withdrawn','excluded','death','completed','stop_medication_completed')"
        ),
        'naproxen_withdrawal_reasons' => array(
            'Description' => 'Naproxen Withdrawal Reason',
            'Type' => "enum('1_voluntary_withdrawal','2_recommended_withdrawal','3_lost_follow_up','4_other')"
        ),
        'probucol_eligibility' => array(
            'Description' => 'Probucol Eligibility',
            'Type' => "enum('yes','no')"
        ),
        'probucol_eligibility_status' => array(
            'Description' => 'Probucol Eligibility Status',
            'Type' => "enum('active','stop_medication_active','withdrawn','excluded','death','completed','stop_medication_completed')"
        ),
        'probucol_withdrawal_reasons' => array(
            'Description' => 'Probucol Withdrawal Reason',
            'Type' => "enum('1_voluntary_withdrawal','2_recommended_withdrawal','3_lost_follow_up','4_other')"
        ),
	'ApoE' => array(
	    'Description' => 'ApoE',
	    'Type' => "varchar(255)"
	),
	'ApoE_112' => array(
	    'Description' => 'ApoE 112',
	    'Type' => "int(10)"
	),
	'ApoE_158' => array(
	    'Description' => 'ApoE 158',
	    'Type' => "int(10)"
	),
	'apoE_allele_no' => array(
	    'Description' => 'ApoE allele number',
	    'Type' => "int(10)"
	),
	'E4_allele_Bin' => array(
	    'Description' => 'E4 allele binary',
	    'Type' => "int(1)"
	),
	'Technicien_ApoE' => array(
	    'Description' => 'Technicien ApoE',
	    'Type' => "varchar(255)"
	),
	'Method_ApoE' => array(
	    'Description' => 'Method ApoE',
	    'Type' => "varchar(255)"
	),
	'Reference_ApoE' => array(
	    'Description' => 'Reference ApoE',
	    'Type' => "date"
	),
	'BchE_K_variant' => array(
	    'Description' => 'BchE K variant',
	    'Type' => "varchar(255)"
	),
	'K_variant_copie_no' => array(
	    'Description' => 'K variant copie number',
	    'Type' => "int(10)"
	),
	'K_variant_bin' => array(
	    'Description' => 'K variant binary',
	    'Type' => "int(1)"
	),
	'Technicien_BchE' => array(
	    'Description' => 'Technicien BchE',
	    'Type' => "varchar(255)"
	),
	'Method_BchE' => array(
	    'Description' => 'Method BchE',
	    'Type' => "varchar(255)"
	),
	'Reference_BchE' => array(
	    'Description' => 'Reference BchE',
	    'Type' => "date"
	),
	'BDNF' => array(
	    'Description' => 'BDNF',
	    'Type' => "varchar(255)"
	),
	'BDNF_allele_no' => array(
	    'Description' => 'BDNF allele number',
	    'Type' => "int(10)"
	),
	'BDNF_copie_bin' => array(
	    'Description' => 'BDNF copie binary',
	    'Type' => "int(1)"
	),
	'Technicien_BDNF' => array(
	    'Description' => 'Technicien BDNF',
	    'Type' => "varchar(255)"
	),
	'Method_BDNF' => array(
	    'Description' => 'Method BDNF',
	    'Type' => "varchar(255)"
	),
	'Reference_BDNF' => array(
	    'Description' => 'Reference BDNF',
	    'Type' => "date"
	),
	'HMGR_Intron_M' => array(
	    'Description' => 'HMGR intron M',
	    'Type' => "varchar(255)"
	),
	'Intron_M_allele_no' => array(
	    'Description' => 'Intron M allele number',
	    'Type' => "int(10)"
	),
	'Intron_M_copie_Bin' => array(
	    'Description' => 'Intron M copie Binary',
	    'Type' => "int(1)"
	),
	'Technicien' => array(
	    'Description' => 'Technicien',
	    'Type' => "varchar(255)"
	),
	'Method' => array(
	    'Description' => 'Method',
	    'Type' => "varchar(255)"
	),
	'Reference_M' => array(
	    'Description' => 'Reference M',
	    'Type' => "date"
	),
        'scan_done' => array(
            'Description' => 'Scan done',
            'Type' => "enum('N', 'Y')"
        ),
    );

    var $Config = array(
        'Meta' => array(
            'DocType' => 'ServerConfig'
        ),
        'Config' => array(
            'GroupString'  => 'How to arrange data: ',
            'GroupOptions' => 
                array('Cross-sectional', 'Longitudinal')
        )
    );

    function __construct() {
        $this->SQLDB = Database::singleton();
        $this->CouchDB = CouchDB::singleton();
    }

    function _getSubproject($id) {
        $config = NDB_Config::singleton();
        $subprojsXML = $config->getSetting("subprojects");
        $subprojs = $subprojsXML['subproject'];
        foreach($subprojs as $subproj) {
            if($subproj['id'] == $id) {
                return $subproj['title'];
            }
        }
    }

    function _getProject($id) {
        $config = NDB_Config::singleton();
        $subprojsXML = $config->getSetting("Projects");
        $subprojs = $subprojsXML['project'];
        foreach($subprojs as $subproj) {
            if($subproj['id'] == $id) {
                return $subproj['title'];
            }
        }
    }

    function _generateQuery() {
        $config = NDB_Config::singleton();
        $fieldsInQuery = "SELECT withdrawal_reasons, naproxen_eligibility, naproxen_eligibility_status, naproxen_withdrawal_reasons, probucol_eligibility, probucol_eligibility_status, probucol_withdrawal_reasons, pso.description,ps.reason_specify, ps.withdrawal_reasons_other_specify, scan_done, ApoE, ApoE_112, ApoE_158, apoE_allele_no, E4_allele_Bin, Technicien_ApoE, Method_ApoE, Reference_ApoE, BchE_K_variant, K_variant_copie_no, K_variant_bin, Technicien_BchE, Method_BchE, Reference_BchE, BDNF, BDNF_allele_no, BDNF_copie_bin, Technicien_BDNF, Method_BDNF, Reference_BDNF, HMGR_Intron_M, Intron_M_allele_no, Intron_M_copie_Bin, Technicien, Method, Reference_M, c.CandID, c.PSCID, s.Visit_label, s.SubprojectID, p.Alias as Site, c.Gender, c.DoB, s.Current_stage, s.Visit, CASE WHEN s.Visit='Failure' THEN 'Failure' WHEN s.Screening='Failure' THEN 'Failure' WHEN s.Visit='Withdrawal' THEN 'Withdrawal' WHEN s.Screening='Withdrawal' THEN 'Withdrawal' ELSE 'Neither' END as Failure, c.ProjectID, pso.Description as Status";
        $tablesToJoin = " FROM session s JOIN candidate c USING (CandID) LEFT JOIN psc p ON (p.CenterID=s.CenterID) LEFT JOIN parameter_type pt_plan ON (pt_plan.Name='candidate_plan') LEFT JOIN parameter_candidate AS pc_plan ON (pc_plan.CandID=c.CandID AND pt_plan.ParameterTypeID=pc_plan.ParameterTypeID) LEFT JOIN participant_status ps ON c.CandID=ps.CandID LEFT JOIN participant_status_options as pso ON ps.participant_status=pso.ID LEFT JOIN genetics as g ON g.Subject_ID=c.PSCID";
        // If proband fields are being used, add proband information into the query
        if ($config->getSetting("useProband") === "true") {
            $probandFields = ", c.ProbandGender as Gender_proband, ROUND(DATEDIFF(c.DoB, c.ProbandDoB) / (365/12)) AS Age_difference";
            $fieldsInQuery .= $probandFields;
        }
        // If expected date of confinement is being used, add EDC information into the query
        if ($config->getSetting("useEDC") === "true") {
            $EDCFields = ", c.EDC as EDC";
            $fieldsInQuery .= $EDCFields;
        }
        $concatQuery = $fieldsInQuery . $tablesToJoin . " WHERE s.Active='Y' AND c.Active='Y' AND c.PSCID <> 'scanner'";
        return $concatQuery;
    }

    function _updateDataDict() {
        $config = NDB_Config::singleton();
        // If proband fields are being used, update the data dictionary
        if ($config->getSetting("useProband") === "true") {
            $this->Dictionary["Gender_proband"] = array(
                'Description' => 'Proband\'s gender',
                'Type' => "enum('Male','Female')"
            );
            $this->Dictionary["Age_difference"] = array(
                'Description' => 'Age difference between the candidate and the proband',
                'Type' => "int"
            );
        }
        // If expected date of confinement is being used, update the data dictionary
        if ($config->getSetting("useEDC") === "true") {
            $this->Dictionary["EDC"] = array(
                'Description' => 'Expected Date of Confinement (Due Date)',
                'Type' => "varchar(255)"
            );
        }
        /*
        // Add any candidate parameter fields to the data dictionary
        $parameterCandidateFields = $this->SQLDB->pselect("SELECT * from parameter_type WHERE SourceFrom='parameter_candidate' AND Queryable=1",
            array());
        foreach($parameterCandidateFields as $field) {
            if(isset($field['Name'])) {
                $fname = $field['Name'];
                $Dict[$fname] = array();
                $Dict[$fname]['Description'] = $field['Description'];
                $Dict[$fname]['Type'] = $field['Type'];
            }
        }
        */
    }

    function run() {
        $config = $this->CouchDB->replaceDoc('Config:BaseConfig', $this->Config);
        print "Updating Config:BaseConfig: $config";

        // Run query
        $demographics = $this->SQLDB->pselect($this->_generateQuery(), array());

        $this->CouchDB->beginBulkTransaction();
        $config_setting = NDB_Config::singleton();
        foreach($demographics as $demographics) {
            $id = 'Demographics_Session_' . $demographics['PSCID'] . '_' . $demographics['Visit_label'];
            $demographics['Cohort'] = $this->_getSubproject($demographics['SubprojectID']);
            unset($demographics['SubprojectID']);
            if(isset($demographics['ProjectID'])) {
                $demographics['Project'] = $this->_getProject($demographics['ProjectID']);
                unset($demographics['ProjectID']);
            }
            if ($config_setting->getSetting("useFamilyID") === "true") {
                $familyID     = $this->SQLDB->pselectOne("SELECT FamilyID FROM family
                                                          WHERE CandID=:cid",
                                                          array('cid'=>$demographics['CandID']));
                if (!empty($familyID)) {
                   $this->Dictionary["FamilyID"] = array(
                                    'Description' => 'FamilyID of Candidate',
                                    'Type'        => "int(6)",
                                    );
                    $demographics['FamilyID'] = $familyID;
                    $familyFields = $this->SQLDB->pselect("SELECT candID as Family_ID,
                                    Relationship_type as Relationship_to_candidate
                                    FROM family
                                    WHERE FamilyID=:fid AND CandID<>:cid",
                                    array('fid'=>$familyID, 'cid'=>$demographics['CandID']));
                    $num_family = 1;
                    if (!empty($familyFields)) {
                        foreach($familyFields as $row) {
                            //adding each sibling id and relationship to the file
                            $this->Dictionary["Family_CandID".$num_family] = array(
                                    'Description' => 'CandID of Family Member '.$num_family,
                                    'Type'        => "varchar(255)",
                                    );
                            $this->Dictionary["Relationship_type_Family".$num_family] = array(
                                    'Description' => 'Relationship of candidate to Family Member '.$num_family,
                                    'Type'        => "enum('half_sibling','full_sibling','1st_cousin')",
                                    );
                            $demographics['Family_CandID'.$num_family]                      = $row['Family_ID'];
                            $demographics['Relationship_type_Family'.$num_family] = $row['Relationship_to_candidate'];
                            $num_family                                                += 1;
                        }
                    }
                }
            }

            $success = $this->CouchDB->replaceDoc($id, array('Meta' => array(
                'DocType' => 'demographics',
                'identifier' => array($demographics['PSCID'], $demographics['Visit_label'])
            ),
                'data' => $demographics
            ));
            print "$id: $success\n";
        }
        $this->_updateDataDict();
        $this->CouchDB->replaceDoc('DataDictionary:Demographics',
                array('Meta' => array('DataDict' => true),
                    'DataDictionary' => array('demographics' => $this->Dictionary)
                    )
                );

        print $this->CouchDB->commitBulkTransaction();

    }
}

// Don't run if we're doing the unit tests; the unit test will call run.
if(!class_exists('UnitTestCase')) {
    $Runner = new CouchDBDemographicsImporter();
    $Runner->run();
}
?>
