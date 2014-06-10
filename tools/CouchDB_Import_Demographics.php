<?
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
        'Site' => array(
            'Description' => 'Site that this visit took place at',
            'Type' => "varchar(3)",
        ),  
        'Current_stage' => array(
            'Description' => 'Current stage of visit',
            'Type' => "enum('Not Started','Screening','Visit','Approval','Subject','Recycling Bin')"
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
	'withdrawal_reasons_other_specify' => array(
	    'Description' => 'Other reason for withdrawal',
	    'Type' => "text"
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
      // 'Project' => array(
       //     'Description' => 'Project for which the candidate belongs',
        //    'Type' => "enum('IBIS1','IBIS2','Fragile X', 'EARLI Collaboration')",
       // ),
  /*      'Plan' => array(
            'Description' => 'Plan for IBIS2 candidate',
            'Type' => "varchar(20)",
        ),
        'EDC' => array(
            'Description' => 'Expected Date of Confinement (Due Date)',
            'Type' => "varchar(255)",
        )*/

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

    function run() {

        $this->CouchDB->replaceDoc('DataDictionary:Demographics',
            array('Meta' => array('DataDict' => true),
                  'DataDictionary' => array('demographics' => $this->Dictionary) 
            )
        );

        // Project, Plan, EDC
        $demographics = $this->SQLDB->pselect("SELECT ps.reason_specify, ps.withdrawal_reasons_other_specify, scan_done, ApoE, ApoE_112, ApoE_158, apoE_allele_no, E4_allele_Bin, Technicien_ApoE, Method_ApoE, Reference_ApoE, BchE_K_variant, K_variant_copie_no, K_variant_bin, Technicien_BchE, Method_BchE, Reference_BchE, BDNF, BDNF_allele_no, BDNF_copie_bin, Technicien_BDNF, Method_BDNF, Reference_BDNF, HMGR_Intron_M, Intron_M_allele_no, Intron_M_copie_Bin, Technicien, Method, Reference_M, c.CandID, c.PSCID, s.Visit_label, s.SubprojectID, p.Alias as Site, c.Gender, s.Current_stage, CASE WHEN s.Visit='Failure' THEN 'Failure' WHEN s.Screening='Failure' THEN 'Failure' WHEN s.Visit='Withdrawal' THEN 'Withdrawal' WHEN s.Screening='Withdrawal' THEN 'Withdrawal' ELSE 'Neither' END as Failure, c.ProjectID, pso.Description as Status FROM session s JOIN candidate c USING (CandID) LEFT JOIN psc p ON (p.CenterID=s.CenterID) LEFT JOIN parameter_type pt_plan ON (pt_plan.Name='candidate_plan') LEFT JOIN parameter_candidate AS pc_plan ON (pc_plan.CandID=c.CandID AND pt_plan.ParameterTypeID=pc_plan.ParameterTypeID) LEFT JOIN participant_status ps ON c.CandID=ps.CandID LEFT JOIN participant_status_options as pso ON ps.participant_status=pso.ID LEFT JOIN genetics as g ON g.Subject_ID=c.PSCID WHERE s.Active='Y' AND c.Active='Y' AND c.PSCID <> 'scanner'", array());
        foreach($demographics as $demographics) {
            $id = 'Demographics_Session_' . $demographics['PSCID'] . '_' . $demographics['Visit_label'];
            $demographics['Cohort'] = $this->_getSubproject($demographics['SubprojectID']);
            unset($demographics['SubprojectID']);
            if(isset($demographics['ProjectID'])) {
                $demographics['Project'] = $this->_getProject($demographics['ProjectID']);
                unset($demographics['ProjectID']);
            }
            $success = $this->CouchDB->replaceDoc($id, array('Meta' => array(
                'DocType' => 'demographics',
                'identifier' => array($demographics['PSCID'], $demographics['Visit_label'])
            ),
                'data' => $demographics
            ));
            print "$id: $success\n";
        }

    }
}
// Don't run if we're doing the unit tests, the unit test will call run..
if(!class_exists('UnitTestCase')) {
    $Runner = new CouchDBDemographicsImporter();
    $Runner->run();
}
?>
