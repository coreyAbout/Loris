<?php

/**
 * The forms for the new profile menu
 * @package main
 */
class NDB_Form_create_timepoint extends NDB_Form
{
    /**
     * The subprojectID being created
     */
    var $subprojectID;

    function _hasAccess()
    {
        // create user object
        $user =& User::singleton();

        $candidate =& Candidate::singleton($this->identifier);

        // check user permissions
    	return ($user->hasPermission('data_entry') && $user->getData('CenterID') == $candidate->getData('CenterID'));
    }

    function _getDefaults()
    {
        $candidate =& Candidate::singleton($this->identifier);

        $defaults['visitLabel'] = $candidate->getNextVisitLabel();
        return $defaults;
    }

    function _process($values)
    {
	$concatTimePoint = $values['visitLabel1']. $values['visitLabel2'] . $values['visitLabel3'];
        $success = TimePoint::createNew($this->identifier, $values['subprojectID'], $concatTimePoint);

        $this->tpl_data['success'] = true;

        // freeze it, just in case
        $this->form->freeze();
    }

    function create_timepoint()
    {
        $config =& NDB_Config::singleton();
        if(!empty($_GET['subprojectID']) && is_numeric($_GET['subprojectID'])){
            $this->subprojectID=$_GET['subprojectID'];
        }

        // cand ID
        $this->tpl_data['candID'] = $this->identifier;
        $this->addHidden('candID', $this->identifier);
        $candidate =& Candidate::singleton($this->identifier);
        $subprojList = null; 
        //List of valid subprojects for a given project
        if($config->getSetting('useProjects') === 'true') {
            $subprojList = $candidate->getValidSubprojects();
        }
        // List of all subprojects from config file
        

        //Loop through the subprojects to get an id out and to create the subproject drop down.
        $allSubprojects = Utility::getSubprojectList();
        $sp_labelOptions = array(null => '');

        foreach($allSubprojects as $subprojectID => $title) {
            if( ! empty($subprojList)) {
                if(in_array($subprojectID, $subprojList)){
                    $sp_labelOptions[$subprojectID] = $title;
                }
            } else {
                $sp_labelOptions[$subprojectID] = $title;
            }
        }
        
        $attributes=array("onchange"=>"location.href='?test_name=create_timepoint&candID=".$this->identifier."&identifier=".$this->identifier."&subprojectID='+this[this.selectedIndex].value;");
        $this->addSelect('subprojectID', 'Subproject', $sp_labelOptions, $attributes);
        $this->_setDefaults(array("subprojectID"=>$this->subprojectID));
        
        
        // visit label
        $visitLabelSettings = $config->getSetting('visitLabel');

        foreach(Utility::toArray($visitLabelSettings) AS $visitLabel){
            if($visitLabel['@']['subprojectID']==$this->subprojectID){
                if ($visitLabel['generation'] == 'user') {
                    $this->addBasicText('visitLabel', 'Visit label', array('size' => $visitLabel['length'] + 1, 'maxlength' => $visitLabel['length']));
                } elseif ($visitLabel['generation'] == 'sequence') {
                    $cohortOptions = array();
                    //$item = Utility::toArray($visitLabel['cohortSet']['item']);
                    //$cohortOptions[$item['@']['value']] = $item['#']; //null;
                    $labelOptions[''] = null;
                    $timePointOptions[''] = null;
                    foreach (Utility::toArray($visitLabel['cohortSet']['item']) AS $item) {
                        $cohortOptions[$item['@']['value']] = $item['#'];
                    }
                    foreach (Utility::toArray($visitLabel['labelSet']['item']) AS $item) {
                        $labelOptions[$item['@']['value']] = $item['#'];
                    }
                    foreach (Utility::toArray($visitLabel['timePoint']['item']) AS $item) {
                        $timePointOptions[$item['@']['value']] = $item['#'];
                    }
                    //$values['visitLabel1'] = $cohort;
                    $this->form->addElement('select', 'visitLabel1', 'Visit label', $cohortOptions);
                    $this->form->addElement('select', 'visitLabel2', 'Visit label', $labelOptions);
                    $this->form->addRule('visitLabel2', 'Visit label is required', 'required');
                    $this->form->addElement('select', 'visitLabel3', 'Visit label', $timePointOptions);
                    $this->form->addRule('visitLabel3', 'Visit label is required', 'required');
                }
            }
        }
    
        $this->form->addFormRule(array(&$this, '_validate'));
    }
    
    
    
    function _validate($values)
    {
        $config =& NDB_Config::singleton();
        $visitLabelSettings = $config->getSetting('visitLabel');
        foreach(Utility::toArray($visitLabelSettings) AS $visitLabel){
            if($visitLabel['@']['subprojectID']==$values['subprojectID']){
                $visitLabelSettings=$visitLabel;
                break;
            }
        }
        
        $errors = array();
        
        // This can happen if the user quickly clicks "Create Time Point" before the page has loaded
        // and the Visit Label dropdowns haven't been selected yet. The page will create "V1" when this
        // is the case without these checks.
        if(empty($values['visitLabel1'])) {
            $errors['visitLabel1'] = 'A cohort label is required for creating a timepoint.';
        }
        if(empty($values['visitLabel2'])) {
            $errors['visitLabel2'] = 'A visit label is required for creating a timepoint.';
        }
        if(empty($values['visitLabel3'])) {
            $errors['visitLabel3'] = 'A timepoint label is required for creating a timepoint.';
        }
        // make sure the user entered a valid visit label
        if ($visitLabelSettings['generation'] == 'user') {
            if (!preg_match($visitLabelSettings['regex'], $values['visitLabel'])) {
                $errors['visitLabel'] = 'This visit label does not match the required structure.  See your project manual for more details.';
            }
        }

        $candidate =& Candidate::singleton($this->identifier);

        $timePointArray = $candidate->getListOfVisitLabels();

        $concatTimePoint = $values['visitLabel1'] . $values['visitLabel2'] . $values['visitLabel3'];

        //If the visitLabel is already in use then let the user pick another
        foreach($timePointArray AS $used_label) {
            if(strcasecmp($concatTimePoint, $used_label) == 0) {
                $errors['visitLabel1'] = 'This visit label does not appear to be unique.';
            }
        }

        //updating this list of valid timepoints should also be updated in the drug_compliance module
	$validTimepoints = array("PREEL00","PREEN00","PREBL00","PRETL06","PREFU12","PRETL18","PREFU24","PRETL30","PREFU36","PRETL42","PRETL42","PREFU48","PRETL54","PRETL66","NAPEN00","NAPBL00","NAPLP00","NAPLA01","NAPTL01","NAPLP03","NAPFU03","NAPFU06","NAPTL09","NAPLP12","NAPFU12","NAPTL15","NAPFU18","NAPTL21","NAPLP24","NAPFU24","NAPTL30","NAPFU36","NAPLP36","NAPTL42","NAPFU48","NAPTL54","NAPTL66","PREAE00","PREAE01","PREAE03","PREAE06","PREAE09","PREAE12","PREAE15","PREAE18","PREAE21","PREAE24","PREAE30","PREAE36","PREAE42","PREAE48","NAPAE00","NAPAE01","NAPAE03","NAPAE06","NAPAE09","NAPAE12","NAPAE15","NAPAE18","NAPAE21","NAPAE24","NAPAE30","NAPAE36","NAPAE42","NAPAE48","NAPTI00", "NAPTL25", "PREAP00", "PREAP01", "PREAP02", "NAPAP00", "NAPAP01", "NAPAP02", "PREEC00", "NAPEC00", "PRELP00","PRELP03", "PRELP12", "PRELP24", "PRELP36", "POIEP00","POIWA00","POIWA01","POIWA02","POIWA03","POICA00","POICA03","POIMA01","POIMA02","POIMA03","POIWB00","POIWB01","POIWB02","POIWB03","POICB00","POICB12","POIMB01","POIMB02","POIMB03","NAPLP48");
        if (!in_array($concatTimePoint,$validTimepoints)) {
            $errors['visitLabel1'] = 'Invalid timepoint!';
        }

        return $errors;
    }

}
?>
