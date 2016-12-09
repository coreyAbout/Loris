<?php
/**
 * The welcome menu
 *
 * @package main
 */
class NDB_Form_statistics extends NDB_Form
{
    var $params = array();

    function _hasAccess()
    {
        $user =& User::singleton();
        return $user->hasPermission('data_entry');
    }

    function _inCenter($centerID, $Centres)
    {
        foreach ($Centres as $Centre) {
            if($Centre['NumericID'] == $centerID) {
                return true;
            }
        }
        return false;
    }

    function render_stats_table($sectionHeader, $tableHeader, $disclamer='', $subcats, $visits, $dropdown_name, $dropdown_opt, $dropdown_selected, $centres, $data, $Subsection="",$projectID=null)
    {
        $tpl_data = array();
        $tpl_data['test_name']  = $_REQUEST['test_name'];
        $tpl_data['Subsection'] = $Subsection;
        $tpl_data['Visits']     = $visits;
        $smarty = new Smarty_neurodb("statistics");
        $tpl_data['SectionHeader']    = $sectionHeader;
        $tpl_data['TableHeader']      = $tableHeader;
        $tpl_data['Disclamer']        = $disclamer;
        $tpl_data['Subcategories']    = $subcats;
        $tpl_data['Subprojects']      = Utility::getSubprojectsForProject($projectID);
        $tpl_data['DropdownName']     = $dropdown_name;
        $tpl_data['DropdownOptions']  = $dropdown_opt;
        $tpl_data['DropdownSelected'] = $dropdown_selected;
        $tpl_data['Centers']          = $centres;
        foreach($data as $row) {
            $subproj = $row['SubprojectID'];
            $vl      = $row['VLabel'];
            $subcat  = $row['Subcat'];
            $center  = $row['CenterID'];

            if(array_search($vl, $visits) !== false
                && array_search($subcat, $subcats) !== false
                && $this->_inCenter($center, $centres) !== false
            ) {
                $tpl_data['data'][$subproj][$vl][$subcat] += $row['val'];
                $tpl_data['data'][$subproj][$vl]['total'] += $row['val'];
                $tpl_data['data'][$subproj][$subcat]      += $row['val'];
                $tpl_data['data'][$subproj]['total']      += $row['val'];
                $tpl_data['data'][$vl][$subcat]           += $row['val'];
                $tpl_data['data'][$vl]['total']           += $row['val'];
                $tpl_data['data'][$subproj]['C' . $center][$vl][$subcat]  = $row['val'];
                $tpl_data['data'][$subproj]['C' . $center][$vl]['total'] += $row['val'];
                $tpl_data['data'][$subproj]['C' . $center][$subcat]      += $row['val'];
                $tpl_data['data'][$subproj]['C' . $center]['total']      += $row['val'];
                $tpl_data['data']['C' . $center][$vl][$subcat]           += $row['val'];
                $tpl_data['data']['C' . $center][$vl]['total']           += $row['val'];
                $tpl_data['data']['C' . $center][$subcat] += $row['val'];
                $tpl_data['data']['C' . $center]['total'] += $row['val'];
                $tpl_data['data'][$subcat] += $row['val'];
                $tpl_data['data']['total'] += $row['val'];
            }

        }
        $smarty->assign($tpl_data);
        $html = $smarty->fetch("table_statistics.tpl");
        return $html;

    }

    function statistics()
    {
        $DB =& Database::singleton();
        $this->tpl_data['StatsTabs'] = $DB->pselect(
            "SELECT ModuleName, SubModuleName, Description
                 FROM StatisticsTabs
                 ORDER BY OrderNo",
            array()
        );

    }

    function stats_general()
    {
        //$DB =& Database::singleton();
    }

    function stats_demographic()
    {
        $DB          =& Database::singleton();
        $config      = NDB_Config::singleton();
        $useProjects = $config->getSetting("useProjects");
        $this->tpl_data['useProjects'] = $useProjects;
        //This boolean is for optional use by project if the demographics table
        // queries any information from the mri_parameter_form table
        $this->tpl_data['mri_table_exists'] = true;
        if (!$DB->tableExists('mri_parameter_form')) {
            $this->tpl_data['mri_table_exists'] = false;
            //return;
        }

        $this->tpl_data['showTable'] = true;

        //PROJECTS
        $projects[null] = 'All Projects';
        foreach(Utility::getProjectList() as $key => $value) {
            $projects[$key] = $value;
        }
        $currentProject = null;
        if(isset($_REQUEST['DemographicProject']) && $_REQUEST['DemographicProject'] != '') {
            $currentProject = $_REQUEST['DemographicProject'];
            $this->tpl_data['CurrentProject'] = array(
                                                 'ID'   => $currentProject,
                                                 'Name' => $projects[$currentProject],
                                                );
            $Param_Project       ='AND (c.ProjectID IS NULL OR c.ProjectID=:pid) ';
            $this->params['pid'] =$_REQUEST['DemographicProject'];
        } else {
            $Param_Project ='';
        }

        //SUBPROJECTS
        $subprojList ="";
        $subprojects = Utility::getSubprojectsForProject($currentProject);
        foreach($subprojects as $key=>$val) {
            $subprojList .= $key.",";
        }
        $subprojList = substr($subprojList, 0, -1);
        if(!empty($subprojList)) {
            $suproject_query ="AND s.SubprojectID IN ($subprojList)";
        } else {
            $suproject_query ='';
        }

        //SITES
        if(isset($_REQUEST['DemographicSite']) && $_REQUEST['DemographicSite'] != '') {
            $Param_Site          ='AND (c.CenterID IS NULL OR c.CenterID=:sid) ';
            $this->params['sid'] =$_REQUEST['DemographicSite'];
        } else {
            $Param_Site = '';
        }
        $centers     = $DB->pselect(
            "SELECT CONCAT('C', CenterID) as ID,
                    CenterID as NumericID,
                    PSCArea as LongName,
                    Name as ShortName
              FROM psc
              WHERE CenterID <> '1'
                AND Study_site = 'Y'",
            array()
        );
        $sites[null] ="All sites";
        foreach ($centers as $row) {
            $sites[$row['NumericID']] = $row['ShortName'];
            if($_REQUEST['DemographicSite'] == $row['NumericID']) {
                $this->tpl_data['CurrentSite'] = array(
                                                  'ID'   => $row['NumericID'],
                                                  'Name' => $row['LongName'],
                                                 );
            }
        }

        $Visits = Utility::getExistingVisitLabels($currentProject);

        $this->tpl_data['Sites']       = $sites;
        $this->tpl_data['Projects']    = $projects;
        $this->tpl_data['Subprojects'] = $subprojects;
        $this->tpl_data['Visits']      = $Visits;

        //REGISTERED CANDIDATES ROW
        $result = $DB->pselect(
            "SELECT s.subprojectid as rowid,
                          count(DISTINCT(c.PSCID)) as val
                      FROM candidate as c
                          LEFT JOIN session s ON (s.CandID=c.CandID)
                      WHERE c.CenterID <> '1'
                          AND c.PSCID <> 'scanner'
                          $Param_Project
                          $Param_Site
                      GROUP BY s.subprojectid",
            $this->params
        );

        foreach($result as $row) {
            $this->tpl_data['registered'][$row['rowid']] = $row['val'];
            $this->tpl_data['registered']['total']      += $row['val'];
        }

        //PARTICIPANT STATUS ACTIVE
        $result = $DB->pselect(
            "SELECT s.subprojectid as rowid,
                          count(DISTINCT(c.PSCID)) as val
                      FROM candidate as c
                          LEFT JOIN session s ON (s.CandID=c.CandID)
                      WHERE c.CenterID <> '1'
                          AND c.PSCID <> 'scanner'
                          AND c.Active='Y'
                          $Param_Project
                          $Param_Site
                      GROUP BY s.subprojectid",
            $this->params
        );

        foreach($result as $row) {
            $this->tpl_data['ps_active'][$row['rowid']] = $row['val'];
            $this->tpl_data['ps_active']['total']      += $row['val'];
        }

        //PARTICIPANT STATUS INACTIVE
        $result = $DB->pselect(
            "SELECT s.subprojectid as rowid,
                          count(DISTINCT(c.PSCID)) as val
                      FROM candidate as c
                          LEFT JOIN session s ON (s.CandID=c.CandID)
                      WHERE c.CenterID <> '1'
                          AND c.PSCID <> 'scanner'
                          AND c.Active<>'Y'
                          $Param_Project
                          $Param_Site
                      GROUP BY s.subprojectid",
            $this->params
        );

        foreach($result as $row) {
            $this->tpl_data['ps_inactive'][$row['rowid']] = $row['val'];
            $this->tpl_data['ps_inactive']['total']      += $row['val'];
        }

        //MALE
        $result = $DB->pselect(
            "SELECT s.subprojectid as rowid,
                          count(DISTINCT(c.PSCID)) as val
                      FROM candidate as c
                          LEFT JOIN session s ON (s.CandID=c.CandID)
                      WHERE coalesce(s.active, 'Y')='Y'
                          AND c.CenterID <> '1'
                          AND c.PSCID <> 'scanner'
                          AND c.Active='Y'
                          AND c.Gender='Male'
                          $Param_Project
                          $Param_Site
                      GROUP BY s.subprojectid",
            $this->params
        );

        foreach($result as $row) {
            $this->tpl_data['gender_male'][$row['rowid']] = $row['val'];
            $this->tpl_data['gender_male']['total']      += $row['val'];
        }
        //FEMALE
        $result = $DB->pselect(
            "SELECT s.subprojectid as rowid,
                          count(DISTINCT(c.PSCID)) as val
                      FROM candidate as c
                          LEFT JOIN session s ON (s.CandID=c.CandID)
                      WHERE coalesce(s.active, 'Y')='Y'
                          AND c.CenterID <> '1'
                          AND c.PSCID <> 'scanner'
                          AND c.Active='Y'
                          AND c.Gender='Female'
                          $Param_Project
                          $Param_Site
                      GROUP BY s.subprojectid",
            $this->params
        );

        foreach($result as $row) {
            $this->tpl_data['gender_female'][$row['rowid']] = $row['val'];
            $this->tpl_data['gender_female']['total']      += $row['val'];
        }

        //AGE AVERAGE
        $result = $DB->pselect(
            "SELECT DISTINCT
                          s.subprojectid as rowid,
                          AVG(DATEDIFF(c.Date_registered,c.DoB)) as age
                      FROM candidate as c
                          JOIN session s ON (s.CandID=c.CandID)
                      WHERE coalesce(s.active, 'Y')='Y'
                          AND c.CenterID <> '1'
                          AND c.PSCID <> 'scanner'
                          AND c.Active='Y'
                          $Param_Project
                          $Param_Site
                      GROUP BY s.subprojectid",
            $this->params
        );

        foreach($result as $row) {
            $this->tpl_data['age_avg'][$row['rowid']] = round($row['age']*12/365, 2);
        }

        //START BIG TABLE
        $inst_dropdown   = array_merge(
            array( '' => 'Recruit Breakdown by Sex'),
            Utility::getAllInstruments()
        );
        $instrument_list = Utility::getAllInstruments();
        $this->tpl_data['all_instruments'] = $instrument_list;
        if(isset($_REQUEST['DemographicInstrument']) && $_REQUEST['DemographicInstrument'] != '') {
            $Subcategories = array(
                              'Complete',
                              'Incomplete',
                             );
            $result        = $DB->pselect(
                "SELECT count(*) as val,
                    f.Data_entry as Subcat,
                    c.CenterID as CenterID,
                    s.SubprojectID as SubprojectID,
                    s.visit_label as VLabel
                FROM session s JOIN candidate c ON (s.CandID=c.CandID)
                    JOIN flag f ON (f.SessionID=s.ID)
                    JOIN ".$DB->escape($_REQUEST[DemographicInstrument])." i USING(CommentID)
                WHERE s.Active='Y'
                    AND c.CenterID <> '1'
                    AND f.Data_entry='Complete'
                    AND f.Administration='All'
                    AND f.CommentID NOT LIKE 'DDE%'
                    $Param_Project
                GROUP BY c.CenterID, SubprojectID, VLabel, Subcat
                UNION
                SELECT count(*) as val, 'Incomplete' as Subcat,
                    c.CenterID as CenterID,
                    s.SubprojectID as SubprojectID,
                    s.visit_label as VLabel
                FROM session s JOIN candidate c USING(CandID)
                    JOIN flag f ON (f.SessionID=s.ID)
                    JOIN ".$DB->escape($_REQUEST[DemographicInstrument])." i USING(CommentID)
                WHERE s.Active='Y' AND s.CenterID <> 1
                    $Param_Project
                    AND f.CommentID NOT LIKE 'DDE%'
                    AND (f.Data_entry IS NULL OR f.Data_entry <> 'Complete')
                GROUP BY c.CenterID, SubprojectID, VLabel, Subcat
                ",
                $this->params
            );
            $this->tpl_data['RecruitsTable'] = $this->render_stats_table(
                "Breakdown of Registered Candidates",
                "Data Entry Completion Status for ". $instrument_list[$_REQUEST['DemographicInstrument']],
                '',
                $Subcategories,
                $Visits,
                "DemographicInstrument",
                $inst_dropdown,
                $_REQUEST['DemographicInstrument'],
                $centers,
                $result,
                "demographics",
                $currentProject
            );
        } else {
            $Subcategories = array(
                              'Male',
                              'Female',
                             );
            $result        = $DB->pselect(
                "SELECT c.CenterID as CenterID,
                    s.SubprojectID as SubprojectID,
                    s.visit_label as VLabel,
                    c.gender as Subcat,
                    count(s.CandID) as val
                FROM session s JOIN candidate c ON (s.CandID=c.CandID)
                WHERE s.active='Y' AND s.CenterID <> '1'
                    AND (s.Current_stage IN ('Visit', 'Screening', 'Approval')
                    $suproject_query)
                    AND COALESCE(s.Screening,'') NOT IN ('Failure', 'Withdrawal')
                    AND COALESCE(s.Visit,'') NOT IN ('Failure', 'Withdrawal')
                GROUP BY c.CenterID, SubprojectID, VLabel, Subcat",
                array()
            );
            $this->tpl_data['RecruitsTable'] = $this->render_stats_table(
                "Breakdown of Registered Candidates",
                "Breakdown by Sex",
                '',
                $Subcategories,
                $Visits,
                "DemographicInstrument",
                $inst_dropdown,
                '',
                $centers,
                $result,
                "demographics",
                $currentProject
            );
        }
    }

    function stats_behavioural()
    {
        $DB          =& Database::singleton();
        $config      = NDB_Config::singleton();
        $useProjects = $config->getSetting("useProjects");
        $this->tpl_data['useProjects'] = $useProjects;
        $subprojList ="";

        $centers = $DB->pselect(
            "SELECT CONCAT('C', CenterID) as ID,
                    CenterID as NumericID,
                    PSCArea as LongName,
                    Name as ShortName
              FROM psc
              WHERE CenterID <> '1'
                    AND Study_site = 'Y'",
            array()
        );

        $this->tpl_data['Centers'] = $centers;

        $projects[null] = 'All Projects';
        foreach(Utility::getProjectList() as $key=>$value) {
            $projects[$key] = $value;
        }
        $this->tpl_data['Projects'] = $projects;
        foreach ($centers as $row) {
            $this->tpl_data['Sites'][$row['NumericID']] = $row['LongName'];
            if($_REQUEST['site'] == $row['NumericID']) {
                $this->tpl_data['CurrentSite'] = array(
                                                  'ID'   => $row['NumericID'],
                                                  'Name' => $row['LongName'],
                                                 );
            }

        }

        if(isset($_REQUEST['BehaviouralProject'])
            && $_REQUEST['BehaviouralProject'] != ''
        ) {
            $currentProject = $_REQUEST['BehaviouralProject'];
            $this->tpl_data['CurrentProject'] = array(
                                                 'ID'   => $currentProject,
                                                 'Name' => $projects[$currentProject],
                                                );
            $Param_Project       ='AND (c.ProjectID IS NULL OR c.ProjectID=:pid) ';
            $this->params['pid'] =$_REQUEST['BehaviouralProject'];
        } else {
            $Param_Project = '';
        }
        $subprojects = Utility::getSubprojectsForProject($currentProject);

        $this->tpl_data['Subprojects'] = $subprojects;
        foreach($subprojects as $key=>$val) {
            $subprojList .= $key.",";
        }
        $subprojList = substr($subprojList, 0, -1);
        if(!empty($subprojList)) {
            $suproject_query ="AND s.SubprojectID IN ($subprojList)";
        } else {
            $suproject_query ='';
        }

        $Visits = Utility::getExistingVisitLabels($currentProject);
        $this->tpl_data['Visits'] = $Visits;

        //---- BEHAVIORAL STATS -----
        $result = $DB->pselect(
            "SELECT s.CenterID,
                    f.Data_Entry as Data_Entry,
                    s.visit_label as VLabel,
                    COUNT(s.CandID) as val
                FROM session s JOIN candidate c ON (s.CandID=c.CandID)
                    JOIN flag f ON (f.SessionID=s.ID)
                WHERE 
                    s.Active='Y' AND c.Active='Y'
                    AND s.Current_stage <> 'Recycling Bin'
                    AND f.CommentID NOT LIKE 'DDE%'
                    $suproject_query
                    $Param_Project
                GROUP by s.ID, s.CenterID, VLabel, f.Data_Entry",
            $this->params
        );

        foreach($result as $row) {
            // Put some things into variables to make the lines shorter
            // so that they don't wrap. Note that "c"(enter array) needs to
            // be a reference since we'll be modifying it.
            $center = $row['CenterID'];
            $vl     = $row['VLabel'];
            $c      =& $this->tpl_data['behaviour']['C' . $center];

            $c[$vl]['total']   += $row['val'];
            $c['all']['total'] += $row['val'];
            if($row['Data_Entry'] == 'Complete') {
                $c[$vl]['complete']   += $row['val'];
                $c['all']['complete'] += $row['val'];
            }
            if($c[$vl]['total'] != 0) {
                $c[$vl]['percent'] =
                    floor($c[$vl]['complete'] / $c[$vl]['total']*100);
            }
            if($c['all']['total'] != 0) {
                $c['all']['percent'] =
                    floor($c['all']['complete'] / $c['all']['total']*100);
            }
        }
        // DDE STATS
        $result = $DB->pselect(
            "SELECT s.CenterID, 
                f.Data_Entry as Data_Entry, 
                s.visit_label as VLabel,
                COUNT(s.CandID) as val
                FROM session as s
                    JOIN candidate as c ON (s.CandID=c.CandID)
                    JOIN flag as f ON (f.SessionID=s.ID)
                WHERE s.Active='Y' 
                AND s.Current_stage <> 'Recycling Bin'
                AND f.CommentID LIKE 'DDE%' 
                AND c.Active='Y'
                $suproject_query
                $Param_Project
                GROUP BY s.CenterID, VLabel, f.Data_Entry ORDER BY c.PSCID;",
            $this->params
        );
        foreach($result as $row) {
            $center = $row['CenterID'];
            $vl     = $row['VLabel'];
            $c      =& $this->tpl_data['dde']['C' . $center];

            $c[$vl]['total']   += $row['val'];
            $c['all']['total'] += $row['val'];
            if($row['Data_Entry'] == 'Complete') {
                $c[$vl]['complete']   += $row['val'];
                $c['all']['complete'] += $row['val'];
            }
            if($c[$vl]['total'] != 0) {
                $c[$vl]['percent'] =
                    floor($c[$vl]['complete'] / $c[$vl]['total']*100);
            }
            if($c['all']['total'] != 0) {
                $c['all']['percent'] =
                    floor($c['all']['complete'] / $c['all']['total']*100);
            }
        }
    }

    function stats_MRI()
    {
        $DB          =& Database::singleton();
        $config      = NDB_Config::singleton();
        $useProjects = $config->getSetting("useProjects");
        $this->tpl_data['useProjects'] = $useProjects;
        $bigTable_params =array();


        $this->tpl_data['showTable'] = true;

        //PROJECTS
        $projects[null] = 'All Projects';
        foreach(Utility::getProjectList() as $key => $value) {
            $projects[$key] = $value;
        }
        $currentProject = null;
        if(isset($_REQUEST['MRIProject']) && $_REQUEST['MRIProject'] != '') {
            $currentProject = $_REQUEST['MRIProject'];
            $this->tpl_data['CurrentProject'] = array(
                                                 'ID'   => $currentProject,
                                                 'Name' => $projects[$currentProject],
                                                );
            $Param_Project          ='AND (c.ProjectID IS NULL OR c.ProjectID=:pid) ';
            $this->params['pid']    =$_REQUEST['MRIProject'];
            $bigTable_params['pid'] =$_REQUEST['MRIProject'];
        } else {
            $Param_Project = '';
        }

        //SUBPROJECTS
        $subprojList ="";
        $subprojects = Utility::getSubprojectsForProject($currentProject);
        foreach($subprojects as $key=>$val) {
            $subprojList .= $key.",";
        }
        $subprojList = substr($subprojList, 0, -1);
        if(!empty($subprojList)) {
            $suproject_query ="AND s.SubprojectID IN ($subprojList)";
        } else {
            $suproject_query ='';
        }
        //SITES
        if(isset($_REQUEST['MRIsite']) && $_REQUEST['MRIsite'] != '') {
            $Param_Site          ='AND (c.CenterID IS NULL OR c.CenterID=:sid) ';
            $this->params['sid'] =$_REQUEST['MRIsite'];
        } else {
            $Param_Site = '';
        }
        $centers     = $DB->pselect(
            "SELECT CONCAT('C', CenterID) as ID,
                    CenterID as NumericID,
                    PSCArea as LongName,
                    Name as ShortName
              FROM psc
              WHERE CenterID <> '1'
                AND Study_site = 'Y'",
            array()
        );
        $sites[null] ="All sites";
        foreach ($centers as $row) {
            $sites[$row['NumericID']] = $row['ShortName'];
            if($_REQUEST['MRIsite'] == $row['NumericID']) {
                $this->tpl_data['CurrentSite'] = array(
                                                  'ID'   => $row['NumericID'],
                                                  'Name' => $row['LongName'],
                                                 );
            }
        }

        $MRISubcategories = array('Complete', 'Partial Run', 'No Scan');
        $MRIOptions = array('T1' => 'T1',
                            'FLAIR' => 'FLAIR',
                            'DTI64' => 'DTI 64',
                            'Fieldmap_DTI' => 'Fieldmap DTI',
                            'Fieldmap_BOLD' => 'Fieldmap BOLD',
                            'BOLD_Encoding_Enrollment' => 'BOLD Encoding_Enrolment',
                            'BOLD_Retrieval_Enrollment' => 'BOLD Retrieval_Enrolment',
                            'BOLD_Resting' => 'BOLD Resting',
                            'BOLD_Encoding' => 'BOLD Encoding',
                            'GRE_T2_star' => 'GRE T2 star',
                            'BOLD_Retrieval' => 'BOLD Retrieval',
                            'ASL' => 'ASL',
        );
        if($_REQUEST['mri_type']) {
            $MRI_Type = $_REQUEST['mri_type'];
            $MRI_Type_Field = $_REQUEST['mri_type'] . "_Scan_Done";
            $MRIHeader = "$MRI_Type Breakdown";
            if($_REQUEST['mri_type'] == 'T1T2') {
                $MRI_Type_Field = "T1_Scan_Done='Complete' and T2_Scan_Done='Complete'";
                $MRIHeader = "T1 and T2 Complete Breakdown";
                $CaseStatement = "
                                CASE($MRI_Type_Field)
                                WHEN True then 'Complete'
                                WHEN False Then 'Incomplete'
                                ELSE 'Incomplete'
                                END";
                $MRISubcategories = array('Complete', 'Incomplete');
            } else {
                $CaseStatement = "
                                CASE($MRI_Type_Field)
                                WHEN 'Partial' THEN 'Partial Run'
                                WHEN 'No' THEN 'No Scan'
                                ELSE $MRI_Type_Field
                                END";
            }
        } else {
            $MRI_Type_Field = 'T1_Scan_Done';
            $MRI_Type = 'T1';
            $MRIHeader = "T1 Scan Done Breakdown";
            $CaseStatement = "
                            CASE($MRI_Type_Field)
                            WHEN 'Partial' THEN 'Partial Run'
                            WHEN 'No' THEN 'No Scan'
                            ELSE $MRI_Type_Field
                            END";
        }
        $scan_done = 'T1_Scan_done';
        $DB->select("SELECT s.SubprojectID,
                            c.CenterID,
                            CASE WHEN length(s.Visit_label) > 2 THEN SUBSTR(s.Visit_label, 4, 2)
                                 WHEN length(s.Visit_label) = 2 THEN s.Visit_label END as VLabel,
                            $CaseStatement as Subcat,
                            COUNT(*) as val
                     FROM mri_parameter_form m JOIN flag f USING (CommentID)
                                               JOIN session s ON (f.SessionID=s.ID)
                                               JOIN candidate c ON (c.CandID=s.CandID)
                     WHERE s.Current_stage <> 'Recycling Bin'
                           AND s.Active='Y'
                           AND c.Active='Y'
                           AND m.CommentID NOT LIKE 'DDE%'
                     GROUP BY Subcat, s.SubprojectID, c.CenterID, VLabel", &$result);
        $M_Visits = Utility::getVisitList();
        $this->tpl_data['MRI_Done_Table'] = $this->render_stats_table($MRIHeader, $MRISubcategories, $M_Visits, "mri_type", $MRIOptions, $MRI_Type, $centers, $result, "mri");

        foreach($result as $row) {
            $center = $row['CenterID'];
            $cohort = $row['SubprojectID'];
            $vl = $row['VLabel'];
            $c =& $this->tpl_data['mri_total'][$cohort]['C' . $center];

            $c[$vl] += $row['val'];
            $this->tpl_data['mri_total']['C' . $center][$vl]['total'] += $row['val'];
            $this->tpl_data['mri_total'][$cohort]['total'] += $row['val'];
            $this->tpl_data['mri_total']['total'] += $row['val'];

        }
        // MRI INTEGRITY STATS
        // MRI parameter form is completed, but nothing in tarchive
        /*
        $DB->select("SELECT COUNT(distinct f.CommentID) as val,
                            s.CenterID as CenterID
                            FROM flag f JOIN session s ON (f.SessionID=s.ID) 
                            LEFT JOIN mri_parameter_form m ON (m.CommentID=f.CommentID)
                            -- LEFT JOIN tarchive t ON (s.CandID=MID(t.PatientName, 9, 6))
                            LEFT JOIN tarchive t ON (s.CandID=MID(t.PatientName, 9, 6) AND MID(t.PatientName, 16, 3)=s.Visit_label)
                            LEFT JOIN candidate c ON (s.CandID=c.CandID)
                            WHERE f.CommentID NOT LIKE 'DDE%' 
                            AND f.Test_name='mri_parameter_form' 
                            and f.Administration <> 'None'
                            and t.TarchiveID IS NULL
                            AND s.Active='Y' 
                            AND t.TarchiveID IS NULL 
                            AND f.Data_entry='Complete'
                            AND f.CommentID NOT LIKE 'DDE%'
                            AND (m.T1_Scan_done IS NULL OR m.T1_Scan_done <> 'No')
                            AND s.SubprojectID IN ($subprojList)
                            GROUP BY s.CenterID;", $result);
        foreach($result as $row) {
            $this->tpl_data['mri_errors'][$row['CenterID']]['no_tarchive'] = $row['val'];
        }
         */

                    // MRI parameter form completed, but nothing in browser
						$result = $DB->pselect("SELECT s.CenterID as CenterID, 
								COUNT(distinct f.CommentID) as val
								FROM mri_parameter_form m LEFT JOIN flag f ON (f.CommentID=m.CommentID)
								LEFT JOIN session s ON (s.ID=f.SessionID) 
								LEFT JOIN files fi ON (fi.SessionID=f.SessionID) 
								WHERE (m.DTI64_Scan_done <> 'No' 
									OR m.T1_Scan_done <> 'No') 
								AND fi.FileID IS NULL 
								AND s.Active='Y' 
								AND s.CenterID <> 1
                                                                AND m.CommentID NOT LIKE 'DDE%' 
                                                                AND s.SubprojectID IN ($subprojList) 
								GROUP by s.CenterID"); //, $result);
						//OR m.T2_Scan_done <> 'No' 
						// AND f.Data_entry = 'Complete'
                    foreach($result as $row) {
                        $this->tpl_data['mri_errors'][$row['CenterID']]['no_browser'] = $row['val'];
                    }

        // MRI Browser populated, but no MRI parameter form completed.
        $DB->select("SELECT s.CenterID as CenterID, COUNT(distinct s.ID) as val
                            FROM files LEFT JOIN session s ON (files.SessionID=s.ID)
                            LEFT JOIN flag f on (f.SessionID=s.ID
                                AND f.Test_name='mri_parameter_form' 
                                AND f.CommentID NOT LIKE 'DDE%') 
                            LEFT JOIN mri_parameter_form mpf ON (mpf.CommentID=f.CommentID)
                            LEFT JOIN candidate c ON (c.CandID=s.CandID)
                            WHERE s.Active='Y'
                            AND (f.ID IS NULL OR f.Data_entry <> 'Complete' OR f.Data_entry IS NULL) 
                            AND c.PSCID <> 'scanner'
                            AND mpf.CommentID NOT LIKE 'DDE%'
                            AND s.SubprojectID IN ($subprojList)
                            $ExtraProject_Criteria
                            GROUP BY s.CenterID", $result);

                    foreach($result as $row) {
                        $this->tpl_data['mri_errors'][$row['CenterID']]['no_parameter'] = $row['val'];
                    }
                    // TOP MRI TABLE

                    // Below this has not been optimized. It's kind of ugly.
						foreach($centers as $center) {
							$id = $center['NumericID'];
							$name = $center['ShortName'];
							//$name = $center['LongName'];
							// -------- MRI STATS---------------

							$result24 = $DB->pselect("SELECT COUNT(s.CandID) FROM session as s, psc as p, candidate as c WHERE c.CandID=s.CandID AND s.Active='Y' AND s.CenterID=p.CenterID  AND s.Scan_done='Y' AND s.SubprojectID IN ($subprojList) AND s.CenterID={$id} ORDER BY s.CandID"); //, $result24);
							if (PEAR::isError($result24)) {
								return PEAR::raiseError("DB Error: ".$result24->getMessage());
							}
							list(,$result24)=each($result24[0]);
							$claimed_count = $result24;

							$result25 = $DB->pselect("SELECT COUNT(s.CandID) FROM mri_parameter_form as pf, session as s, flag as f, psc as p, candidate as c WHERE f.CommentID=pf.CommentID AND s.ID=f.SessionID AND c.CandID=s.CandID AND s.Active='Y' AND s.CenterID=p.CenterID AND pf.Data_entry_completion_status='Complete' AND s.CenterID={$id} AND s.SubprojectID IN ($subprojList) ORDER BY p.Name"); //, $result25);
							if (PEAR::isError($result25)) {
								return PEAR::raiseError("DB Error: ".$result25->getMessage());
							}
							list(,$result25)=each($result25[0]);
							$forms_count = $result25;
							$three_scans = $DB->pselectOne("SELECT count(distinct CandID) FROM session vEN join flag fEN ON (fEN.SessionID=vEN.ID and Test_name='mri_parameter_form' AND CommentID NOT LIKE 'DDE%') join mri_parameter_form mEN ON (fEN.CommentID=mEN.CommentID) 
									WHERE vEN.Active='Y' AND vEN.Visit_label LIKE '%EN%' and mEN.T1_Scan_Done='Complete'
									AND vEN.CenterID='{$id}' AND vEN.SubprojectID IN ($subprojList)");

							$mri_data[] = array('name' => $name,  'claimed_count' => $claimed_count, 'forms_count' => $forms_count, 'all_three_scans' => $three_scans);

							// AND EXISTS (SELECT 'x' FROM session v12 join flag f12 ON (f12.SessionID=v12.ID and Test_name='mri_parameter_form' AND CommentID NOT LIKE 'DDE%') join mri_parameter_form m12 ON (f12.CommentID=m12.CommentID)
							//               WHERE v12.Active='Y' AND v12.Cancelled='N' AND v12.Visit_label='v12' AND v06.CandID=v12.CandID AND m12.T1_Scan_Done='Complete') 
							//  AND EXISTS (SELECT 'x' FROM session v24 join flag f24 ON (f24.SessionID=v24.ID and Test_name='mri_parameter_form' AND CommentID NOT LIKE 'DDE%') join mri_parameter_form m24 ON (f24.CommentID=m24.CommentID)
							//                WHERE v24.Active='Y' AND v24.Cancelled='N' AND v24.Visit_label='v24' AND v06.CandID=v24.CandID and m24.T1_Scan_Done='Complete') 
						}
                    $subprojectList = "'" . implode("','", Utility::getSubprojectList()) . "'";

						// $MultiTimepoint = $DB->pselect("SELECT psc.PSCArea as Center, SUM(Three_T1_done) as T1, SUM(Three_DTI_Done) as DTI, SUM(Three_All_done) as Three_All_done FROM 
						//          (SELECT c.CenterID, COUNT(DISTINCT c.CandID)," .
						//SUM(Three_T2_Done) as T2 ,
						/* This essentially does COUNT(T1_Scan_done='Complete'), but because of the way COUNT(expr) works, 
						 * we need to make sure that anything that isn't 'Complete' is evaluated as NULL (not false), hence
						 * the crazy CASE statement. The different count statements Do v06, v06+v12, v06+v12+v24, all 3 for T2, all
						 * three for DTI, and all three for T1+T2+DTI. The outer select statement sums up all the groupings, to get
						 * a final scalar value.
						 */

						/*  "COUNT(DISTINCT CASE (COALESCE(mpf06.T1_Scan_done, 'No')='Complete') WHEN true THEN c.CandID ELSE null END) as v06_Done, 
						    FROM candidate c JOIN session v06 ON (c.CandID=v06.CandID AND v06.Visit_label LIKE '%EN%' and v06.Active='Y' and v06.Current_stage <> 'Recycling Bin') 
						    JOIN flag f06 ON (f06.SessionID=v06.ID and f06.Test_name='mri_parameter_form' AND f06.CommentID NOT LIKE 'DDE%') 
						    JOIN mri_parameter_form mpf06 ON (f06.CommentID=mpf06.CommentID) 
						    WHERE     c.Active='Y' AND c.PSCID  not like 'dcc%'  and c.PSCID <> 'scanner' 
						    GROUP BY c.CenterID) breakdown JOIN psc ON (breakdown.CenterID=psc.CenterID) WHERE psc.CenterID <> 1 GROUP BY psc.Name ORDER BY psc.CenterID", array()); */

						// COUNT(DISTINCT CASE (COALESCE(mpf12.T2_Scan_done, 'No')='Complete' AND COALESCE(mpf06.T2_Scan_done, 'No')='Complete' 
						//           AND COALESCE(mpf24.T2_Scan_done, 'No')='Complete') WHEN true THEN c.CandID ELSE null END) as Three_T2_done, 
						// AND COALESCE(mpf12.T2_Scan_done, 'No')='Complete' AND COALESCE(mpf06.T2_Scan_done, 'No')='Complete' 
						//  AND COALESCE(mpf24.T2_Scan_done, 'No')='Complete' 

						$scans = array('T1_Scan_done', 'FLAIR_Scan_done', 'DTI64_Scan_done', 'Fieldmap_DTI_Scan_done', 'Fieldmap_BOLD_Scan_done', 'BOLD_Resting_Scan_done', 'GRE_T2_star_Scan_done', 'ASL_Scan_done');
						$sum = 0;
						$sum_files = 0;
						$count = array();
						$total = array('Name' => 'Total', 'T1' => '0', 'FLAIR' => 0, 'DTI64' => 0, 'Fieldmap_DTI'=>0, 'Fieldmap_BOLD'=>0, 'BOLD_Resting'=>0, 'GRE_T2_star'=>0, 'ASL'=>0, 'All' => 0);
						$scans_inserted = array('Name' => 'Total', 'T1' => '0', 'FLAIR' => 0, 'DTI64' => 0, 'Fieldmap_DTI'=>0, 'Fieldmap_BOLD'=>0, 'BOLD_Resting'=>0, 'GRE_T2_star'=>0, 'ASL'=>0, 'All' => 0);
						$count_pass = array('Name' => 'Total', 'T1' => '0', 'FLAIR' => 0, 'DTI64' => 0, 'Fieldmap_DTI'=>0, 'Fieldmap_BOLD'=>0, 'BOLD_Resting'=>0, 'GRE_T2_star'=>0, 'ASL'=>0, 'All' => 0);
						$total_files = array('Name' => 'Total', 'T1' => '0', 'FLAIR' => 0, 'DTI64' => 0, 'Fieldmap_DTI'=>0, 'Fieldmap_BOLD'=>0, 'BOLD_Resting'=>0, 'GRE_T2_star'=>0, 'ASL'=>0, 'All' => 0);
						$total_pass = array('Name' => 'Total', 'T1' => '0', 'FLAIR' => 0, 'DTI64' => 0, 'Fieldmap_DTI'=>0, 'Fieldmap_BOLD'=>0, 'BOLD_Resting'=>0, 'GRE_T2_star'=>0, 'ASL'=>0, 'All' => 0);

						foreach (Utility::getSiteList() as $site) {
							$row = array(null=>"");
							if ($site != 'DCC') {
								$row['Center'] = $site;
								$sum = 0;
								$sum_files = 0;
								foreach ($scans as $s) {
									$type = preg_replace('/_Scan_done/', '', $s);
									$count = $DB->pselectRow("SELECT $s,  psc.Name as Center, COUNT(DISTINCT c.CandID) as count FROM candidate c 
											JOIN session s ON (c.CandID=s.CandID and s.Active='Y' and s.Current_stage <> 'Recycling Bin') 
											JOIN flag f ON (f.SessionID=s.ID and f.Test_name='mri_parameter_form' AND f.CommentID NOT LIKE 'DDE%') 
											JOIN mri_parameter_form m ON (f.CommentID=m.CommentID)  
											JOIN psc on c.CenterID = psc.CenterID
											WHERE     c.Active='Y' AND c.PSCID  not like 'dcc%'  
											AND c.PSCID <> 'scanner' 
											AND psc.Name = '$site' 
											AND m.$s = 'Complete' 
											GROUP BY c.CenterID ORDER BY c.CenterID");
									if (empty($count))
										$count['count'] = 0;
									$row[$type] = $count['count'];
									$sum += $row[$type];
									//print_r($count);
								}
								// 

								//foreach ($MultiTimepoint as $row) 
								$this->tpl_data['mri_all_scans_done'][] = array(
										'Name' => $row['Center'],
										'T1' => $row['T1'],
										'FLAIR' => $row['FLAIR'],
										'DTI64' => $row['DTI64'],
										'Fieldmap_DTI' => $row['Fieldmap_DTI'],
										'Fieldmap_BOLD' => $row['Fieldmap_BOLD'],
										'BOLD_Resting' => $row['BOLD_Resting'],
										'GRE_T2_star' => $row['GRE_T2_star'],
										'ASL' => $row['ASL'],
										'All' => $sum
										);
								$total['T1'] += $row['T1'];
								$total['FLAIR'] += $row['FLAIR'];
								$total['DTI64'] += $row['DTI64'];
								$total['Fieldmap_DTI'] += $row['Fieldmap_DTI'];
								$total['Fieldmap_BOLD'] += $row['Fieldmap_BOLD'];
								$total['BOLD_Resting'] += $row['BOLD_Resting'];
								$total['GRE_T2_star'] += $row['GRE_T2_star'];
								$toal['ASL'] += $row['ASL'];
								$total['All'] += $sum;

								/*                                $files = $DB->pselect("select m.Scan_type as type, f.File, count(*) as c, 
												  CASE WHEN length(s.Visit_label) > 2 THEN SUBSTR(s.Visit_label, 4, 2) 
												  WHEN length(s.Visit_label) = 2 THEN s.Visit_label END as VLabel
												  from files f join mri_scan_type m on m.ID = f.AcquisitionProtocolID 
												  join session s on f.sessionID = s.ID join psc on s.CenterID = psc.CenterID 
												  where s.Active = 'Y' and psc.Name = '$site' group by m.Scan_type");*/
								$files = $DB->pselect("select m.Scan_type as type, count(*) as c, 
										CASE WHEN length(s.Visit_label) > 2 THEN SUBSTR(s.Visit_label, 4, 2) 
										WHEN length(s.Visit_label) = 2 THEN s.Visit_label END as VLabel
										FROM files f 
										JOIN mri_scan_type m on m.ID = f.AcquisitionProtocolID 
										JOIN session s on f.sessionID = s.ID 
										JOIN psc on s.CenterID = psc.CenterID 
										WHERE s.Active = 'Y' and psc.Name = '$site' and s.Visit_label NOT LIKE '%phantom%' 
										GROUP by m.Scan_type, VLabel
										ORDER BY Scan_type");
								//print_r($files);

								$pass = $DB->pselect("select count(mriQCStatus) as count_pass, scan_type as type, 
										CASE WHEN length(s.Visit_label) > 2 THEN SUBSTR(s.Visit_label, 4, 2) 
										WHEN length(s.Visit_label) = 2 THEN s.Visit_label END as VLabel
										FROM psc p join session s using(CenterID) join files f on s.ID = f.SessionID 
										JOIN mri_scan_type m on f.AcquisitionProtocolID=m.ID 
										WHERE mriQCStatus = 'Pass' and p.Name = '$site' 
										GROUP BY scan_type, VLabel");
								/*$this->tpl_data['mri_all_scans_inserted'][] = array( 
								  'Name'  =>$site,
								  'T1'    =>100, 
								  'FLAIR' =>0,
								  'DTI64' =>0,
								  'Fieldmap_DTI'=>0, 
								  'Fieldmap_BOLD'=>0, 
								  'BOLD_Resting'=>0,   
								  'GRE_T2_star'=>0,                                    
								  'ASL'=>0                                    
								  );

								  $this->tpl_data['count_pass'][] = array( 
								  'Name'  =>$site,
								  'T1'    =>0, 
								  'FLAIR' =>0,
								  'DTI64' =>0,
								  'Fieldmap_DTI'=>0, 
								  'Fieldmap_BOLD'=>0, 
								  'BOLD_Resting'=>0,   
								  'GRE_T2_star'=>0,                                    
								  'ASL'=>0                                    
								  );*/

								$mappings = array('T1'=>'adniT1', 'FLAIR'=>'FLAIR', 'DTI64'=>'DTI', 'Fieldmap_BOLD'=>'Fieldmap', 'Fieldmap_DTI'=>'Fieldmap', 'BOLD_Resting'=>'Resting', 'GRE_T2_star'=>'greT2star', 'ASL'=>'ASL');
								//foreach($mappings as $k=>$v) {
								// } 
								foreach ($files as $file) {
									$scans_inserted['Name'] = $site;
									//$total_files[
									if ($file['type'] == 'adniT1') { 
										$scans_inserted['T1'] += $file['c'];
										$total_files['T1'] += $file['c'];
										$sum_files += $file['c'];
									}
									if ($file['type'] == 'FLAIR') {
										$scans_inserted['FLAIR'] = $file['c'];
										$total_files['FLAIR'] += $file['c'];
										$sum_files += $file['c'];
									}
									if ($file['type'] == 'DTI') {
										//  $this->tpl_data['mri_all_scans_inserted'][]['DTI64'] = $file['c'];
										$scans_inserted['DTI64'] = $file['c'];
										$total_files['DTI64'] += $file['c'];
										$sum_files += $file['c'];
									}
									if ($file['type'] == 'Fieldmap') {
											//$path = preg_replace('/\/data\/preventAD\/data\/assembly\/[0-9]+/', '', $file['File']);
											//$parts = explode('/', $path);
											//$visit_label = $parts[1];


										if ($file['VLabel'] == 'EN') {
											$scans_inserted['Fieldmap_DTI'] += $file['c'];
											$total_files['Fieldmap_DTI'] += $file['c'];
										} else {
											$scans_inserted['Fieldmap_BOLD'] += $file['c'];
											$total_files['Fieldmap_BOLD'] += $file['c'];
										}
										$sum_files += $file['c'];
									}   
									if ($file['type'] == 'Resting') {
										// $this->tpl_data['mri_all_scans_inserted'][]['BOLD_Resting'] = $file['c'];
										$scans_inserted['BOLD_Resting'] += $file['c'];
										$total_files['BOLD_Resting'] += $file['c'];
										$sum_files += $file['c'];
									}
									if ($file['type'] == 'greT2star') {
										// $this->tpl_data['mri_all_scans_inserted'][]['GRE_T2_star'] = $file['c'];
										$scans_inserted['GRE_T2_star'] += $file['c'];
										$total_files['GRE_T2_star'] += $file['c'];
										$sum_files += $file['c'];
									}
									if ($file['type'] == 'ASL') {
										// $this->tpl_data['mri_all_scans_inserted'][]['GRE_T2_star'] = $file['c'];
										$scans_inserted['ASL'] += $file['c'];
										$total_files['ASL'] += $file['c'];
										$sum_files += $file['c'];
									}
								}

								$sum_pass = 0;
								foreach ($pass as $p) {
									$count_pass['Name'] = $site;
									if ($p['type'] == 'adniT1') { 
										$count_pass['T1'] += $p['count_pass'];
										$total_pass['T1'] += $p['count_pass'];
										$sum_pass += $p['count_pass'];
									}
									if ($p['type'] == 'FLAIR') {
										$count_pass['FLAIR'] += $p['count_pass'];
										$total_pass['FLAIR'] += $p['count_pass'];
										$sum_pass += $p['count_pass'];
									}
									if ($p['type'] == 'DTI') {
										$count_pass['DTI64'] += $p['count_pass'];
										$total_pass['DTI64'] += $p['count_pass'];
										$sum_pass += $p['count_pass'];
									}
									if ($p['type'] == 'Fieldmap') {
										if ($p['VLabel'] == 'EN') {
											$count_pass['Fieldmap_DTI'] += $p['count_pass'];
											$total_pass['Fieldmap_DTI'] += $p['count_pass'];
										} else {
											$count_pass['Fieldmap_BOLD'] += $p['count_pass'];
											$total_pass['Fieldmap_BOLD'] += $p['count_pass'];
										}
										$sum_pass += $p['count_pass'];
									}   
									if ($p['type'] == 'Resting') {
										$count_pass['BOLD_Resting'] += $p['count_pass'];
										$total_pass['BOLD_Resting'] += $p['count_pass'];
										$sum_pass += $p['count_pass'];
									}
									if ($p['type'] == 'greT2star') {
										$count_pass['GRE_T2_star'] += $p['count_pass'];
										$total_pass['GRE_T2_star'] += $p['count_pass'];
										$sum_pass += $p['count_pass'];
									}
									if ($p['type'] == 'ASL') {
										$count_pass['ASL'] += $p['count_pass'];
										$total_pass['ASL'] += $p['count_pass'];
										$sum_pass += $p['count_pass'];
									}
								}
							}
						}
						$scans_inserted['All'] = $sum_files; 
						$count_pass['All'] = $sum_pass; 
						$total_files['All'] = $sum_files; 
						$total_pass['All'] = $sum_pass; 

						//                        $this->tpl_data['mri_all_scans_done'][] = $total;
						$this->tpl_data['mri_all_scans_inserted'][] = $scans_inserted;
						$this->tpl_data['count_pass'][] = $count_pass;

						$this->tpl_data['mri_total_scans_done'][] = $total;
						$this->tpl_data['mri_total_scans_inserted'] = $total_files;
						$this->tpl_data['total_pass'] = $total_pass;

						$this->tpl_data['mri_data'] = $mri_data;


						//MRI Feedback
						/*$feedback_comments = $DB->pselect("SELECT m.scan_type, fp.Comment, fi.File, count(f.PredefinedCommentID) as count 
						  from mri_scan_type m join files fi on m.ID = fi.AcquisitionProtocolID 
						  join feedback_mri_comments f on fi.FileID=f.FileID join feedback_mri_predefined_comments fp on f.PredefinedCommentID = fp.PredefinedCommentID 
						  WHERE f.SessionID IS NULL 
						  AND f.PredefinedCommentID = '4' OR f.PredefinedCommentID = '6' OR f.PredefinedCommentID = '8' OR f.PredefinedCommentID = '9' OR f.PredefinedCommentID = '36' 
						  GROUP BY f.PredefinedCommentID, m.scan_type");*/ 
						$feedback_comments = $DB->pselect("SELECT m.scan_type, fp.Comment, count(f.PredefinedCommentID) as count, 
								CASE WHEN length(s.Visit_label) > 2 THEN SUBSTR(s.Visit_label, 4, 2) 
								WHEN length(s.Visit_label) = 2 THEN s.Visit_label END as VLabel
								FROM mri_scan_type m join files fi on m.ID = fi.AcquisitionProtocolID 
								JOIN session s on fi.sessionID = s.ID
								JOIN feedback_mri_comments f on fi.FileID=f.FileID 
								JOIN feedback_mri_predefined_comments fp on f.PredefinedCommentID = fp.PredefinedCommentID 
								WHERE f.SessionID IS NULL 
								AND f.PredefinedCommentID = '4' OR f.PredefinedCommentID = '6' OR f.PredefinedCommentID = '8' OR f.PredefinedCommentID = '9' OR f.PredefinedCommentID = '36' 
								GROUP BY f.PredefinedCommentID, m.scan_type, VLabel"); 

							$comments = array();
						//print_r($feedback_comments);
						foreach ($feedback_comments as $f=>$g)
						{
							if (!array_key_exists($g['Comment'], $comments)) {
								$comments[$g['Comment']] = array('T1'=>0, 'FLAIR'=>0, '64 DTI'=>0, 'DTI Fieldmap'=>0, 'BOLD Fieldmap'=>0, 'Resting BOLD'=>0, 'GRE T2*'=>0, 'ASL'=>0, 'All'=>0);
							}
							if ($g['scan_type'] == 'adniT1') 
								$comments[$g['Comment']]['T1'] += $g['count'];
							if ($g['scan_type'] == 'DTI') 
								$comments[$g['Comment']]['64 DTI'] += $g['count'];
							if ($g['scan_type'] == 'FLAIR')
								$comments[$g['Comment']]['FLAIR'] += $g['count'];
							if ($g['scan_type'] == 'Fieldmap') {
								//	$path = preg_replace('/\/data\/preventAD\/data\/assembly\/[0-9]+/', '', $g['File']);
								if ($g['VLabel'] == 'EN')
									$comments[$g['Comment']]['DTI Fieldmap'] += $g['count'];
								else 
									$comments[$g['Comment']]['BOLD Fieldmap'] += $g['count'];
							}		
							if ($g['scan_type'] == 'Resting')  
								$comments[$g['Comment']]['Resting BOLD'] += $g['count'];
							if ($g['scan_type'] == 'greT2star')  
								$comments[$g['Comment']]['GRE T2*'] += $g['count'];
							if ($g['scan_type'] == 'ASL') 
								$comments[$g['Comment']]['ASL'] += $g['count'];
							//			else
							//				$comments[$g['Comment']][$g['scan_type']] += $g['count'];
							$comments[$g['Comment']]['All'] += $g['count'];

							if ($g['scan_type'] == 'adniT1') 
								$new_feedback['T1'][$g['Comment']] += $g['count'];
							elseif ($g['scan_type'] == 'DTI') 
								$new_feedback['64 DTI'][$g['Comment']] += $g['count'];
							elseif ($g['scan_type'] == 'Fieldmap') { 
								$new_feedback['DTI Fieldmap'][$g['Comment']] += $g['count'];
								$new_feedback['BOLD Fieldmap'][$g['Comment']] += $g['count'];
							}
							elseif ($g['scan_type'] == 'Resting')  
								$new_feedback['Resting BOLD'][$g['Comment']] += $g['count'];
							elseif ($g['scan_type'] == 'greT2star')  
								$new_feedback['GRE T2*'][$g['Comment']] += $g['count'];
							else
								$new_feedback[$g['scan_type']][$g['Comment']] += $g['count'];
							//foreach($g as $comment=>$count) {
							//$new_feedback[$g['Comment']] = $g['count'];
							//$this->tpl_data['mri_all_scans_done'][] = array(    
							//}
							foreach($mappings as $label=>$field) {
								if (!array_key_exists($new_feedback, $label))
									$new_feedback[$label] = 0;
							}

						}
						//print_r($new_feedback);
						//print_r($comments);
						$this->tpl_data['feedback_mri_comments'] = $comments;

	} // End function

        //GET SCAN TYPES
        $Scan_type_results = $DB->pselect(
            "SELECT mst.ID, mst.Scan_type
              FROM mri_scan_type mst
                JOIN mri_protocol mp ON (mst.ID=mp.Scan_type)",
            array()
        );

        foreach ($Scan_type_results as $row) {
            $scan_types[$row['ID']] = $row['Scan_type'];
        }

        $scans_selected =array();
        if(isset($_REQUEST['Scans']) && $_REQUEST['Scans'] != '') {
            $scans_selected_input = explode(",", $_REQUEST['Scans']);
        }
        if (empty($scans_selected_input)) {
            $scans_selected =$scan_types;
        } else {
            foreach($scans_selected_input as $key => $scid)
            {
                $scans_selected[$scid] =$scan_types[$scid];
            }
        }

        $Visits = Utility::getExistingVisitLabels($currentProject);
        $this->tpl_data['scan_types']     = $scan_types;
        $this->tpl_data['Scans_sel_box']  = array_keys($scans_selected);
        $this->tpl_data['Scans_selected'] = $scans_selected;
        $this->tpl_data['Sites']          = $sites;
        $this->tpl_data['Projects']       = $projects;
        $this->tpl_data['Subprojects']    = $subprojects;
        $this->tpl_data['Visits']         = $Visits;

        //START BIG TABLE
        //if table is not in database handle by displaying error message
        $this->tpl_data['mri_table_exists'] = true;
        if (!$DB->tableExists('mri_parameter_form')) {
            $this->tpl_data['mri_table_exists'] = false;
            return;
        }
        $MRISubcategories = array(
                             'Complete',
                             'Partial Run',
                             'No Scan',
                            );

        //Check if a specific scan is requested otherwise display first
        //available scan
        if(!$_REQUEST['mri_type']) {
            $MRI_Type = current($scan_types);
        } else {
            $MRI_Type = $scan_types[$_REQUEST['mri_type']];
        }
        //TODO IBIS
        if ($MRI_Type==='t1w') {
            $MRI_Type ='T1';
        } elseif ($MRI_Type=='t2w') {
            $MRI_Type ='T2';
        } elseif ($MRI_Type=='dti') {
            $MRI_Type ='DTI';
        } elseif ($MRI_Type=='ep2d_bold') {
            $MRI_Type ='BOLD';
        } elseif ($MRI_Type=='DTI65' || $MRI_Type=='DTI65_B1000' ) {
            $MRI_Type ='Spectroscopy';
        }

        $MRI_Type_Field = $MRI_Type . "_Scan_Done";
        $MRIHeader      = "$MRI_Type Breakdown";
        $CaseStatement  = "
                          CASE($MRI_Type_Field)
                          WHEN 'Partial' THEN 'Partial Run'
                          WHEN 'No' THEN 'No Scan'
                          ELSE $MRI_Type_Field
                          END";

        $result = $DB->pselect(
            "SELECT s.SubprojectID,
                    c.CenterID,
                    s.Visit_label as VLabel,
                    $CaseStatement as Subcat,
                    COUNT(*) as val
              FROM mri_parameter_form m
                    JOIN flag f USING (CommentID)
                    JOIN session s ON (f.SessionID=s.ID)
                    JOIN candidate c ON (c.CandID=s.CandID)
              WHERE s.Current_stage <> 'Recycling Bin'
                    AND f.Administration <> 'None'
                    AND s.Active='Y'
                    AND c.Active='Y'
                    AND f.CommentID NOT LIKE 'DDE%'
                    $suproject_query
                    $Param_Project
              GROUP BY Subcat,
                    s.SubprojectID,
                    c.CenterID,
                    s.Visit_label",
            $bigTable_params
        );

        $M_Visits = Utility::getExistingVisitLabels($currentProject);
        $this->tpl_data['MRI_Done_Table'] = $this->render_stats_table(
            "Breakdown By Scan Type",
            $MRIHeader,
            "",
            $MRISubcategories,
            $M_Visits,
            "mri_type",
            $scan_types,
            $_REQUEST['mri_type'],
            $centers,
            $result,
            "mri",
            $currentProject
        );
        //END BIGTABLE

        //considers the naming convention of mri_parameter_form is "scanType_scan_done"
        $Scan_data_results =array();
        foreach ($scans_selected as $key => $scan) {
            $scan_params = array_merge(array('scan' => $scan), $this->params);

            //INSERT COUNT TOTAL
            $Scan_data_results[$key]['insert_count']['total'] = $DB->pselectOne(
                "SELECT COUNT(*)
                FROM files fi
                    JOIN session s ON (fi.SessionID=s.ID)
                    JOIN candidate c ON (c.CandID=s.CandID)
                WHERE s.Current_stage <> 'Recycling Bin'
                    AND s.Active='Y'
                    AND c.Active='Y'
                    AND fi.FileType='mnc'
                    AND fi.File LIKE CONCAT('%_', :scan, '_%')
                    $suproject_query
                    $Param_Project
                    $Param_Site
                ",
                $scan_params
            );
            //INSERT COUNT Values
            $count_data = $DB->pselect(
                "SELECT s.SubprojectID as subID, COUNT(*) as cnt
                FROM files fi
                    JOIN session s ON (fi.SessionID=s.ID)
                    JOIN candidate c ON (c.CandID=s.CandID)
                WHERE s.Current_stage <> 'Recycling Bin'
                    AND s.Active='Y'
                    AND c.Active='Y'
                    AND fi.FileType='mnc'
                    AND fi.File LIKE CONCAT('%_', :scan, '_%')
                    $suproject_query
                    $Param_Project
                    $Param_Site
                GROUP BY s.SubprojectID",
                $scan_params
            );
            foreach ($count_data as $row){
                $Scan_data_results[$key]['insert_count'][$row['subID']] = $row['cnt'];
                //no_qc_count is computed by the difference between insertions
                // and the sum of passed qc and failed qc
                $Scan_data_results[$key]['no_qc_count'][$row['subID']] = $row['cnt'];
            }

            //QC STATUS: PASSED TOTAL
            $Scan_data_results[$key]['qc_pass_count']['total'] = $DB->pselectOne(
                "SELECT COUNT(*)
                FROM files_qcstatus fqc
                	JOIN files fi on (fi.FileID=fqc.FileID)
                    JOIN session s ON (fi.SessionID=s.ID)
                    JOIN candidate c ON (c.CandID=s.CandID)
                WHERE s.Current_stage <> 'Recycling Bin'
                    AND fqc.FileId IS NOT NULL
                    AND s.Active='Y'
                    AND c.Active='Y'
                    AND fqc.QCStatus='Pass'
                    AND fi.File LIKE CONCAT('%_', :scan, '_%')
                    $suproject_query
                    $Param_Project
                    $Param_Site
                ",
                $scan_params
            );
            //QC STATUS: PASSED values
            $count_data = $DB->pselect(
                "SELECT s.SubprojectID as subID, COUNT(*) as cnt
                FROM files_qcstatus fqc
                	JOIN files fi on (fi.FileID=fqc.FileID)
                    JOIN session s ON (fi.SessionID=s.ID)
                    JOIN candidate c ON (c.CandID=s.CandID)
                WHERE s.Current_stage <> 'Recycling Bin'
                    AND fqc.FileId IS NOT NULL
                    AND s.Active='Y'
                    AND c.Active='Y'
                    AND fqc.QCStatus='Pass'
                    AND fi.File LIKE CONCAT('%_', :scan, '_%')
                    $suproject_query
                    $Param_Project
                    $Param_Site
                GROUP BY s.SubprojectID",
                $scan_params
            );
            foreach ($count_data as $row){
                $Scan_data_results[$key]['qc_pass_count'][$row['subID']] = $row['cnt'];
                //no_qc_count is computed by the difference between insertions
                // and the sum of passed qc and failed qc
                $Scan_data_results[$key]['no_qc_count'][$row['subID']] =
                    $Scan_data_results[$key]['no_qc_count'][$row['subID']]-$row['cnt'];
            }

            //QC STATUS: FAILED TOTAL
            $Scan_data_results[$key]['qc_fail_count']['total'] = $DB->pselectOne(
                "SELECT COUNT(*)
                FROM files_qcstatus fqc
                	JOIN files fi on (fi.FileID=fqc.FileID)
                    JOIN session s ON (fi.SessionID=s.ID)
                    JOIN candidate c ON (c.CandID=s.CandID)
                WHERE s.Current_stage <> 'Recycling Bin'
                    AND fqc.FileId IS NOT NULL
                    AND s.Active='Y'
                    AND c.Active='Y'
                    AND fqc.QCStatus='Fail'
                    AND fi.File LIKE CONCAT('%_', :scan, '_%')
                    $suproject_query
                    $Param_Project
                    $Param_Site
                ",
                $scan_params
            );
            //QC STATUS: FAILED values
            $count_data = $DB->pselect(
                "SELECT s.SubprojectID as subID, COUNT(*) as cnt
                FROM files_qcstatus fqc
                	JOIN files fi on (fi.FileID=fqc.FileID)
                    JOIN session s ON (fi.SessionID=s.ID)
                    JOIN candidate c ON (c.CandID=s.CandID)
                WHERE s.Current_stage <> 'Recycling Bin'
                    AND fqc.FileId IS NOT NULL
                    AND s.Active='Y'
                    AND c.Active='Y'
                    AND fqc.QCStatus='Fail'
                    AND fi.File LIKE CONCAT('%_', :scan, '_%')
                    $suproject_query
                    $Param_Project
                    $Param_Site
                GROUP BY s.SubprojectID",
                $scan_params
            );
            foreach ($count_data as $row){
                $Scan_data_results[$key]['qc_fail_count'][$row['subID']] = $row['cnt'];
                //no_qc_count is computed by the difference between insertions
                // and the sum of passed qc and failed qc
                $Scan_data_results[$key]['no_qc_count'][$row['subID']] =
                    $Scan_data_results[$key]['no_qc_count'][$row['subID']]-$row['cnt'];
            }

            $Scan_data_results[$key]['no_qc_count']['total'] =
                $Scan_data_results[$key]['insert_count']['total'] -
                $Scan_data_results[$key]['qc_pass_count']['total'] -
                $Scan_data_results[$key]['qc_fail_count']['total'];
        }
        $this->tpl_data['scan_data_results'] = $Scan_data_results;

    } // End function



    //RELIABILITY
    function stats_reliability()
    {
        $DB          =& Database::singleton();
        $config      = NDB_Config::singleton();
        $useProjects = $config->getSetting("useProjects");
        $this->tpl_data['useProjects'] = $useProjects;
        //PROJECTS
        $projects[null] = 'All Projects';
        foreach(Utility::getProjectList() as $key => $value) {
            $projects[$key] = $value;
        }

        if(isset($_REQUEST['ReliabilityProject']) && $_REQUEST['ReliabilityProject'] != '') {
            $currentProject = $_REQUEST['ReliabilityProject'];
            $this->tpl_data['CurrentProject'] = array(
                                                 'ID'   => $currentProject,
                                                 'Name' => $projects[$currentProject],
                                                );
            $Param_Project       ='AND (c.ProjectID IS NULL OR c.ProjectID=:pid) ';
            $this->params['pid'] =$_REQUEST['ReliabilityProject'];
        } else {
            $Param_Project ='';
        }

        //SITES
        if(isset($_REQUEST['ReliabilitySite']) && $_REQUEST['ReliabilitySite'] != '') {
            $Param_Site          ='AND (c.CenterID IS NULL OR c.CenterID=:sid) ';
            $this->params['sid'] =$_REQUEST['ReliabilitySite'];
        } else {
            $Param_Site = '';
        }
        $centers     = $DB->pselect(
            "SELECT CONCAT('C', CenterID) as ID,
                    CenterID as NumericID,
                    PSCArea as LongName,
                    Name as ShortName
              FROM psc
              WHERE CenterID <> '1'
                AND Study_site = 'Y'",
            array()
        );
        $sites[null] ="All sites";
        foreach ($centers as $row) {
            $sites[$row['NumericID']] = $row['ShortName'];
            if($_REQUEST['ReliabilitySite'] == $row['NumericID']) {
                $this->tpl_data['CurrentSite'] = array(
                                                  'ID'   => $row['NumericID'],
                                                  'Name' => $row['LongName'],
                                                 );
            }
        }

        $this->tpl_data['Centers']  = $centers;
        $this->tpl_data['Projects'] = $projects;
        $this->tpl_data['Sites']    = $sites;

        $this->tpl_data['reliability_completion'] = $this->getReliabilityData($Param_Project, $Param_Site);
    }

    function getReliabilityData($Param_Project, $Param_Site)
    {
        // Whether something is reliable or not isn't stored in the database,
        // but calculated on the fly.
        // As a result, we need to manually calculate it and maintain for each
        // type of thing Tanya wants stats for here separately

        // Thresholds was copy/pasted from
        include_once __DIR__ . "/../../reliability/php/NDB_Menu_Filter_reliability.class.inc";
        $thresholds = NDB_Menu_Filter_Reliability::getThreshholds();

        $config            = NDB_Config::singleton();
        $reliabilityConfig = $config->getSetting("ReliabilityInstruments");
        $tests = array();

        foreach(Utility::toArray($reliabilityConfig) AS $reliabilityInstrument){
            $instrument_list = $reliabilityInstrument['Instrument'];
            foreach($instrument_list AS $instrument){
                $tests[$instrument['Testname']] = $instrument['Displayname'];
            }
        }

        $reliable = array();
        foreach($tests as $inst => $name) {
            $reliable[] = $this->_getSingleReliabilityData(
                $Param_Project,
                $Param_Site,
                $name,
                $inst,
                $thresholds[$inst]
            );
        }

        return array_merge($reliable);
    }
    function _getSingleReliabilityData($Param_Project, $Param_Site, $name, $inst_name, $cutoff)
    {
        $db          = Database::singleton();
        $ret         = array();
        $ret['name'] = $name;
        $ret['complete'] = $db->pselectOne(
            "SELECT count(*)
                                          FROM reliability r
                                              JOIN flag f USING(CommentID)
                                              JOIN session s ON (s.ID=f.SessionID)
                                              JOIN candidate c ON (c.CandID=s.CandID)
                                          WHERE Instrument='$inst_name'
                                              AND Invalid <> 'yes'
                                              AND Reliability_score IS NOT NULL
                                              AND c.CenterID <> 1
                                              $Param_Project
                                              $Param_Site",
            $this->params
        );

        $ret['reliable'] = $db->pselectOne(
            "SELECT count(*)
                                          FROM reliability r
                                              JOIN flag f USING(CommentID) JOIN session s ON (s.ID=f.SessionID)
                                              JOIN candidate c ON (c.CandID=s.CandID)
                                          WHERE Instrument='$inst_name'
                                              AND Invalid <> 'yes'
                                              AND Reliability_score IS NOT NULL
                                              AND Reliability_score > $cutoff
                                              AND c.CenterID <> 1
                                              $Param_Project
                                              $Param_Site",
            $this->params
        );

        $ret['total'] = $db->pselectOne(
            "SELECT count(*)
                                          FROM reliability r
                                              JOIN flag f USING(CommentID)
                                              JOIN session s ON (s.ID=f.SessionID)
                                              JOIN candidate c ON (c.CandID=s.CandID)
                                          WHERE Instrument='$inst_name'
                                              AND Invalid <> 'yes'
                                              AND c.CenterID <> 1
                                              $Param_Project
                                              $Param_Site",
            $this->params
        );

        if($ret['total'] == 0) {
            // These should probably be indeterminate instead of 0, but 0
            // looks cleaner.
            $ret['percent_complete'] = '0';
            $ret['percent_reliable'] = '0';
        } else {
            $ret['percent_complete'] = round($ret['complete'] / $ret['total'] * 100);
            $ret['percent_reliable'] = round($ret['reliable'] / $ret['total'] * 100);
        }
        return $ret;
    }

    /**
     * Include the column formatter required to display the feedback link colours
     * in the statistics menu
     *
     * @return array of javascript to be inserted
     */
    function getJSDependencies()
    {
        $factory = NDB_Factory::singleton();
        $baseURL = $factory->settings()->getBaseURL();
        $deps    = parent::getJSDependencies();
        return array_merge(
            $deps,
            array(
             $baseURL . "/statistics/js/table_statistics.js",
             $baseURL . "/statistics/js/statistics.js",
             $baseURL . "/statistics/js/form_stats_behavioural.js",
             $baseURL . "/statistics/js/form_stats_reliability.js",
             $baseURL . "/statistics/js/form_stats_MRI.js",
             $baseURL . "/statistics/js/form_stats_demographic.js",
            )
        );
    }
} // End class
?>
