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

        //Convert select multiple elements into database storable values
        if(!empty($this->multipleSelect)){
            foreach($this->multipleSelect AS $elname){
                if(isset($values[$elname]) && is_array($values[$elname])){
                    $values[$elname]=implode("{@}",$values[$elname]);
                }
            }
        }

        $values['related_participant_CandID'] = $DB->pselectOne(
            'SELECT CandID FROM candidate where PSCID = :related_participant_PSCID',
            array('related_participant_PSCID'=>$values['related_participant_PSCID'])
        );

        if ($values['related_participant_status'] == 'sibling' || $values['related_participant_status'] == 'parent' || $values['related_participant_status'] == 'child') {
            $values['related_participant_status_degree'] = '1st degree relative';
        } elseif ($values['related_participant_status'] == 'aunt/uncle' || $values['related_participant_status'] == 'niece/nephew' || $values['related_participant_status'] == 'half-sibling') {
            $values['related_participant_status_degree'] = '2nd degree relative';
        } elseif ($values['related_participant_status'] == 'first-cousin') {
            $values['related_participant_status_degree'] = '3rd degree relative';
        } else {
            $values['related_participant_status_degree'] = 'other';
        } 

        // save the values
        //insert it
        $success = $DB->insert("drug_compliance", $values);

        if (Utility::isErrorX($success)) {
            return PEAR::raiseError(
                "NDB_Form_drug_compliance::_process: ".$success->getMessage()
            );
        }


        // automatically populate/save related participant's information
        if ($values['related_participant_status'] == 'parent') {
            $values['related_participant_status'] = 'child';
        } elseif ($values['related_participant_status'] == 'child') {
            $values['related_participant_status'] = 'parent';
        } elseif ($values['related_participant_status'] == 'aunt/uncle') {
            $values['related_participant_status'] = 'niece/nephew';
        } elseif ($values['related_participant_status'] == 'niece/nephew') {
            $values['related_participant_status'] == 'aunt/uncle';
        }

        $tmp = $values['CandID'];
        $values['CandID'] = $values['related_participant_CandID'];
        $values['related_participant_CandID'] = $tmp;
        $values['related_participant_PSCID'] = $DB->pselectOne(
            'SELECT PSCID FROM candidate where CandID = :related_participant_CandID',
            array('related_participant_CandID'=>$values['related_participant_CandID'])
        );

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

        $this->addBasicText(
            'visit_label',
            'Visit Label, if applicable:'
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

        $result = $DB->pselect("SELECT * FROM drug_compliance WHERE CandID=:cid AND drug=:drug", array('cid'=>$this->identifier,'drug'=>'naproxen'));
        $this->tpl_data['history_list_naproxen'] = $result;

        $result = $DB->pselect("SELECT * FROM drug_compliance WHERE CandID=:cid AND drug=:drug", array('cid'=>$this->identifier,'drug'=>'probucol'));
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

        if ($fields['related_participant_PSCID'] == null) {
            $errors['related_participant_PSCID'] = 'Required!';
        }
        if ($fields['related_participant_status'] == null) {
            $errors['related_participant_status'] = 'Required!';
        }

        $DB =& Database::singleton();
        $pscid = $DB->pselectOne(
            'SELECT PSCID FROM candidate where CandID = :Candid',
            array('Candid'=>$this->identifier)
        );
        if ($fields['related_participant_PSCID'] == $pscid) {
            $errors['related_participant_PSCID'] = "Can't select the same PSCID!";
        }

        // if related participant status is 'other', related_participant_status_specify must be populated
        if ($fields['related_participant_status'] == "other" && $fields['related_participant_status_specify'] == null) {
            $errors['related_participant_status_specify'] = 'Required if Related Participant Status is "Other"';
        }

        return $errors;
    }

}
?>
