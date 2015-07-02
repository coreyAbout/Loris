<?php
/**
 * This form contains the code for the drug compliance module.
 * This module allows you to enter drug compliance for each candidate.
 * Data entered here can be treated as independent of being tied
 * to any specific visit(s).
 *
 * PHP Version 5
 *
 *  @category Behavioural 
 *  @package  Main
 *  @author   Justin Kat <justinkat@gmail.com>
 *  @license  Loris License
 *  @link     https://github.com/Jkat/Loris
 */
require_once "NDB_Form.class.inc";
require_once "Utility.class.inc";


class NDB_Form_drug_compliance extends NDB_Form
{
    var $indent = "&nbsp;&nbsp;&nbsp;&nbsp;";

    /**
     * Determine if the user should have access to this page.
     * Currently tied to data_entry permission.
     *
     * @return boolean True if the user should have access
     */
    function _hasAccess()
    {
        // create user object
        $user =& User::singleton();
        if (Utility::isErrorX($user)) {
            return PEAR::raiseError("User Error: ".$user->getMessage());
        }

        // check that the $candID is passed b/c the profile needs to be opened
        if ($this->identifier != $_REQUEST['candID']) {
            return PEAR::raiseError(
                "NDB_Form_drug_compliance::_access: " 
                . "Candidate Profile Error ($this->identifier)"
            );
        }
        // check user permissions
        return ($user->hasPermission('data_entry'));
    }

    /**
     * Gets default options for smarty to fill in.
     *
     * @return array Currently saved entries in database
     */
    function _getDefaults()
    {
        return $defaults;
    }
    /**
     * Process the submitted values, called after validation.
     *
     * @param array $values The values submitted by the user 
     *                      to the form.
     *
     * @return boolean True on success
     */
    function _process($values)
    {
        if (!is_array($values) || count($values) === 0) {
            return true;
        }
        $DB =& Database::singleton();

        $values['CandID'] = $this->identifier;

        if (!(is_null($_SESSION['State']))) {
            $currentUser =& User::singleton($_SESSION['State']->getUsername());
            $id = $currentUser->getData("UserID");
            $values['entry_staff'] = $id;
        }

        if ($values['drug_issued_date_status'] == 'not_answered' ) {
            $values['drug_issued_date'] = null;
        } else {
            $values['drug_issued_date'] = sprintf("%04d-%02d-%02d", $values['drug_issued_date']['Y'], $values['drug_issued_date']['M'], $values['drug_issued_date']['d']);
        }
        if ($values['drug_returned_date_status'] == 'not_answered' ) {
            $values['drug_returned_date'] = null;
        } else {
            $values['drug_returned_date'] = sprintf("%04d-%02d-%02d", $values['drug_returned_date']['Y'], $values['drug_returned_date']['M'], $values['drug_returned_date']['d']);
        }

        // begin compliance calculations

        //don't calculate compliance if don't know pills returned or pills issued
        if ($values['pills_returned_status'] === "not_answered" || $values['pills_issued_status'] === "not_answered") {
            $values['compliance_evaluation'] = null;
            $values['behavioral_compliance_evaluation'] = null;
        }

        $drug_issued_date = $values['drug_issued_date'];
        $drug_returned_date = $values['drug_returned_date'];
        $datetime1 = new DateTime($drug_issued_date);
        $datetime2 = new DateTime($drug_returned_date);
        $interval = $datetime1->diff($datetime2,true);
        $expected = $interval->days * 2;
        $pills_issued = $values['pills_issued'];
        $pills_returned = $values['pills_returned'];
        $taken = $pills_issued - $pills_returned;
        $values['compliance_evaluation'] = ($taken / $expected) * 100;

        $interruption_from_date = array();
        $interruption_to_date = array();
        $candid = $this->identifier;
        $sessions = $DB->pselect("SELECT id from session where candid=:candidate",array('candidate'=>$this->identifier));
        foreach ($sessions as $key => $value) {
                if ($treatment_interruption_commentID = $DB->pselectOne("SELECT CommentID from flag where test_name='treatment_interruption' and sessionID={$value['id']}",array()))
                        break;
        }

        //if no interruptions, compliance = behavioral compliance
        if (empty($treatment_interruption_commentID)) {
                $values['behavioral_compliance_evaluation'] = $values['compliance_evaluation'];
        } else {
                for ($i = 1; $i <= 30; $i++) {
                        $interruption_from_date[] = $DB->pselectOne("SELECT {$i}_interruption_from_date from treatment_interruption where CommentID =:cid",array("cid"=>$treatment_interruption_commentID));
                        $interruption_to_date[] = $DB->pselectOne("SELECT {$i}_interruption_to_date from treatment_interruption where CommentID =:cid",array("cid"=>$treatment_interruption_commentID));
                        $interruption_from_date_status[] = $DB->pselectOne("SELECT {$i}_interruption_to_date_status from treatment_interruption where CommentID =:cid",array("cid"=>$treatment_interruption_commentID));
                        $interruption_to_date_status[] = $DB->pselectOne("SELECT {$i}_interruption_to_date_status from treatment_interruption where CommentID =:cid",array("cid"=>$treatment_interruption_commentID));
                        $treatment_termination[] = $DB->pselectOne("SELECT {$i}_treatment_termination from treatment_interruption where CommentID =:cid",array("cid"=>$treatment_interruption_commentID));
                }
                //$visit_date = $DB->pselectOne("SELECT Date_taken from $this->table where CommentID =:cid",array("cid"=>$this->commentID));

                $enoughInfoBehavioral = true;
                foreach ($interruption_from_date as $key => $value) {
                        if ($treatment_termination[$key] == 'no' && $interruption_to_date_status[$key] == 'not_answered') {
                                if (strtotime($value) >= strtotime($drug_issued_date) && strtotime($value) <= strtotime($drug_returned_date)) {
                                        $enoughInfoBehavioral = false;
                                        break;
                                } elseif (strtotime($interruption_to_date[$key]) >= strtotime($drug_issued_date) && strtotime($interruption_to_date[$key]) <= strtotime($drug_returned_date)) {
                                        $enoughInfoBehavioral = false;
                                        break;
                                }
                        }
                }

                $interruptions = array();
                if ($enoughInfoBehavioral) {
                        foreach ($interruption_from_date as $key => $value) {
                                if ($treatment_termination[$key] == 'yes' && $interruption_to_date_status[$key] == 'not_answered') {
                                        $interruption_to_date[$key] = $drug_returned_date;
                                }
                                if (strtotime($value) >= strtotime($drug_issued_date) && strtotime($value) <= strtotime($drug_returned_date)) {
                                        $last_date = strtotime($interruption_to_date[$key]) < strtotime($drug_returned_date) ? $interruption_to_date[$key] : $drug_returned_date;
                                        $start_date = new DateTime($value);
                                        $last_date = new DateTime($last_date);
                                        if ((strtotime($interruption_to_date[$key]) >= strtotime($drug_returned_date)) && (strtotime($value) == strtotime($drug_issued_date))) {
                                                $interruptions[] = (($start_date->diff($last_date,true)->days+1) * 2) - 2;
                                        } elseif (strtotime($interruption_to_date[$key]) >= strtotime($drug_returned_date)) {
                                                $interruptions[] = (($start_date->diff($last_date,true)->days+1) * 2) - 1;
                                        } elseif (strtotime($value) == strtotime($drug_issued_date)) {
                                                $interruptions[] = (($start_date->diff($last_date,true)->days+1) * 2) - 1;
                                        } else {
                                                $interruptions[] = ($start_date->diff($last_date,true)->days+1) * 2;
                                        }
                                } elseif (strtotime($interruption_to_date[$key]) >= strtotime($drug_issued_date) && strtotime($value) <= strtotime($drug_issued_date)) {
                                        $last_date = strtotime($interruption_to_date[$key]) < strtotime($drug_returned_date) ? $interruption_to_date[$key] : $drug_returned_date;
                                        $start_date = new DateTime($drug_issued_date);
                                        $last_date = new DateTime($last_date);
                                        $interruptions[] = (($start_date->diff($last_date,true)->days+1) * 2) - 1;
                                }

                        }
                        foreach ($interruptions as $key => $value) {
                                $expected = $expected - $value;
                        }
                        $values['behavioral_compliance_evaluation'] = ($taken / $expected) * 100;
                } else {
                        $values['behavioral_compliance_evaluation'] = null;
                }
        }


        // save the values by inserting
        $success = $DB->insert("drug_compliance", $values);

        if (Utility::isErrorX($success)) {
            return PEAR::raiseError(
                "NDB_Form_drug_compliance::_process: ".$success->getMessage()
            );
        }

        $this->tpl_data['success'] = true;
        $this->form->freeze();
    }

    /**
     * Constructor called by loris to setup the tpl_data
     * array
     *
     * @return null
     */
    function drug_compliance()
    {

        $this->tpl_data['candID'] = $this->identifier;

        $config =& NDB_Config::singleton();
        $DB =& Database::singleton();

        //entry staff info
        if (!(is_null($_SESSION['State']))) {
            $currentUser =& User::singleton($_SESSION['State']->getUsername());
            $id = $currentUser->getData("UserID");
            $entry_staff = $id;
        }
        $this->addScoreColumn('entry_staff', 'Data Entry Staff');
        $this->tpl_data['entry_staff'] = $entry_staff;

        $pscid = $DB->pselectOne(
            'SELECT PSCID FROM candidate where CandID = :Candid',
            array('Candid'=>$this->identifier)
        );
        $this->addScoreColumn('pscid', 'PSCID');
        $this->tpl_data['pscid'] = $pscid;

        $drug = array(null=>null,'naproxen'=>'Naproxen','probucol'=>'Probucol');
        $this->addSelect(
            'drug',
            "Drug",
            $drug
        );

        $config =& NDB_Config::singleton();
        $dateOptions = array(
        'language' => 'en',
        'format'   => 'YMd',
        'minYear'  => $config->getSetting('startYear'),
        'maxYear'  => $config->getSetting('endYear'),
        'addEmptyOption' => true,
        'emptyOptionValue' => null
                );

        $group[] = $this->createDate(
            "drug_issued_date",
            null,
            $dateOptions,
            array(
             'class' => 'form-control input-sm '."drug_issued_date",
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $group[] = $this->createSelect(
            "drug_issued_date_status",
            null,
            array(
             null           => "",
             'not_answered' => "Not Answered",
            ),
            array(
             'class' => 'form-control input-sm not-answered',
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $this->addGroup(
            $group,
            "drug_issued_date_group",
            "Date Issued",
            "",
            false
        );
        unset($group);

        $group[] = $this->createText(
            "pills_issued",
            null,
            array(
             'class' => 'form-control input-sm '."pills_issued",
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $group[] = $this->createSelect(
            "pills_issued_status",
            null,
            array(
             null           => "",
             'not_answered' => "Not Answered",
            ),
            array(
             'class' => 'form-control input-sm not-answered',
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $this->addGroup(
            $group,
            "pills_issued_group",
            "Number of Pills Issued",
            "",
            false
        );
        unset($group);

        $group[] = $this->createDate(
            "drug_returned_date",
            null,
            $dateOptions,
            array(
             'class' => 'form-control input-sm '."drug_returned_date",
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $group[] = $this->createSelect(
            "drug_returned_date_status",
            null,
            array(
             null           => "",
             'not_answered' => "Not Answered",
            ),
            array(
             'class' => 'form-control input-sm not-answered',
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $this->addGroup(
            $group,
            "drug_returned_date_group",
            "Date Returned",
            "",
            false
        );
        unset($group);

        $group[] = $this->createText(
            "pills_returned",
            null,
            array(
             'class' => 'form-control input-sm '."pills_returned",
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $group[] = $this->createSelect(
            "pills_returned_status",
            null,
            array(
             null           => "",
             'not_answered' => "Not Answered",
            ),
            array(
             'class' => 'form-control input-sm not-answered',
             'style' => 'max-width:25%; display:inline-block;',
            )
        );
        $this->addGroup(
            $group,
            "pills_returned_group",
            "Number of Pills Returned",
            "",
            false
        );
        unset($group);

        //this list of visit labels should be kept inline with the list in the project create_timepoint class
        $visit_labels = array(null=>null,'not_applicable'=>"Not Applicable","PRETL06"=>"PRETL06","PREFU12"=>"PREFU12","PRETL18"=>"PRETL18","PREFU24"=>"PREFU24","PRETL30"=>"PRETL30","PREFU36"=>"PREFU36","PRETL42"=>"PRETL42","PREFU48"=>"PREFU48","NAPLP00"=>"NAPLP00","NAPLA01"=>"NAPLA01","NAPTL01"=>"NAPTL01","NAPLP03"=>"NAPLP03","NAPFU03"=>"NAPFU03","NAPFU06"=>"NAPFU06","NAPTL09"=>"NAPTL09","NAPLP12"=>"NAPLP12","NAPFU12"=>"NAPFU12","NAPTL15"=>"NAPTL15","NAPFU18"=>"NAPFU18","NAPTL21"=>"NAPTL21","NAPLP24"=>"NAPLP24","NAPFU24"=>"NAPFU24","NAPTL30"=>"NAPTL30","NAPFU36"=>"NAPFU36","NAPTL42"=>"NAPTL42","NAPFU48"=>"NAPFU48","NAPTL25"=>"NAPTL25","PREAP00"=>"PREAP00","PREAP01"=>"PREAP01","NAPAP00"=>"NAPAP00","NAPAP01"=>"NAPAP01",'not_answered'=>"Not Answered");
        $this->addSelect(
            'visit_label',
            "Visit Label of Drug Return",
            $visit_labels
        );

        $this->addTextAreaGroup(
            'comments',
            'Comments:'//,
//            array(null => null,'not_answered'=>"Not Answered")
        );

        $this->drug_compliance_details();

        $this->form->addFormRule(array(&$this,'_validate'));
    }


    function drug_compliance_details() {
        
        $this->tpl_data['candID'] = $_REQUEST['candID'];
        $this->addHeader('Drug Compliance for Candidate');
        $DB =& Database::singleton();
        $pscid = $DB->pselectOne(
                'SELECT PSCID FROM candidate where CandID = :candid',
                array('candid'=>$this->identifier)
                );

        $result = $DB->pselect("SELECT drug_issued_date, pills_issued, drug_returned_date, pills_returned, compliance_evaluation, behavioral_compliance_evaluation, visit_label, entry_staff FROM drug_compliance WHERE CandID=:cid AND drug=:drug ORDER BY drug_issued_date asc", array('cid'=>$this->identifier,'drug'=>'naproxen'));
        $this->tpl_data['history_list_naproxen'] = $result;

        $result = $DB->pselect("SELECT drug_issued_date, pills_issued, drug_returned_date, pills_returned, compliance_evaluation, behavioral_compliance_evaluation, visit_label, entry_staff FROM drug_compliance WHERE CandID=:cid AND drug=:drug ORDER BY drug_issued_date asc", array('cid'=>$this->identifier,'drug'=>'probucol'));
        $this->tpl_data['history_list_probucol'] = $result;

    }

    /** 
     * This function is for validation of user input, depending on
     * the questions answered.
     *
     * @param array $fields The list of input to the form submitted by
     *                      smarty.
     *
     * @return array An array of elements with errors. An empty array if
     *               all elements are valid.
     */
    function _validate($fields)
    {
        $errors=array();

        if ($fields['drug'] == null) {
            $errors['drug'] = 'Required!';
        }
        if (($fields['drug_issued_date_status'] == null) && ($fields['drug_issued_date']['Y'] == "" || $fields['drug_issued_date']['M'] == "" || $fields['drug_issued_date']['d'] == "")) {
            $errors['drug_issued_date_group'] = 'Required!';
        }
        if ($fields['pills_issued'] == null && $fields['pills_issued_status'] == null) {
            $errors['pills_issued_group'] = 'Required!';
        }
        if ($fields['pills_issued_status'] == null && !is_numeric($fields['pills_issued']) && $fields['pills_issued'] != null) {
            $errors['pills_issued_group'] = 'Numeric values only!';
        }
        if (($fields['drug_returned_date_status'] == null) && ($fields['drug_returned_date']['Y'] == "" || $fields['drug_returned_date']['M'] == "" || $fields['drug_returned_date']['d'] == "")) {
            $errors['drug_returned_date_group'] = 'Required!';
        }
        if ($fields['pills_returned'] == null && $fields['pills_returned_status'] == null) {
            $errors['pills_returned_group'] = 'Required!';
        }
        if ($fields['pills_returned_status'] == null && !is_numeric($fields['pills_returned']) && $fields['pills_returned'] != null) {
            $errors['pills_returned_group'] = 'Numeric values only!';
        }
        if ($fields['visit_label'] == null) {
            $errors['visit_label'] = 'Required!';
        }

        return $errors;
    }

}
?>
