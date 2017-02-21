<?php

/**
 * This class features the code for the menu portion of the Loris imaging
 * browser.
 *
 * PHP Version 5
 *
 *  @category   Behavioural
 *  @package    Main
 *  @subpackage Imaging 
 *  @author     Dave MacFarlane <driusan@bic.mni.mcgill.ca>
 *  @license    @GPLv3
 *  @link       https://www.github.com/aces/Loris-Trunk/
 */

require_once __DIR__ . '/Imaging_Session_ControlPanel.class.inc';
/**
 * Provides the PHP code for the menu filter for the imaging browser
 *
 *  @category   Behavioural
 *  @package    Main
 *  @subpackage Imaging
 *  @author     Dave MacFarlane <driusan@bic.mni.mcgill.ca>
 *  @license    @GPLv3
 *  @link       https://www.github.com/aces/Loris-Trunk/
 */
class NDB_Menu_Filter_Imaging_Browser extends NDB_Menu_Filter
{
    var $AjaxModule = true;
    /**
     * Determine whether the user has permission to view this page
     *
     * @return bool whether the user hass access
     */
    function _hasAccess()
    {
        $user =& User::singleton();

        // allow only to view own site data
        $site =& Site::singleton($user->getData('CenterID'));

        return ($user->hasPermission('imaging_browser_view_allsites') || ($site->isStudySite() && $user->hasPermission('imaging_browser_view_site')));
    }

    /**
     * Set up the variables required by NDB_Menu_Filter class for constructing
     * a query
     *
     * @return null
     */
    function _setupVariables()
    {
        $NewDataSubquery = "CASE 
            COALESCE((
                SELECT MIN(QCLastChangeTime) 
                FROM files LEFT JOIN files_qcstatus 
                USING(FileID) 
                WHERE files.SessionID=s.ID 
                AND OutputType='native' 
                AND AcquisitionProtocolID not in (1, 2, 3, 52) 
                GROUP BY QCLastChangeTime 
                ORDER BY QCLastChangeTime limit 1
            ), 'new')
            WHEN 'new' THEN 'new' 
            WHEN '' THEN 'new'
            ELSE ''
            END";
        $T1PassSubquery = "CASE 
            COALESCE((
                SELECT MIN(files_qcstatus.QCStatus+0) 
                FROM files JOIN files_qcstatus 
                USING (FileID) 
                WHERE files.SessionID=s.ID AND files.AcquisitionProtocolID=44 and (files_qcstatus.QCStatus=1 or files_qcstatus.QCStatus=2)
                GROUP BY files.SessionID), '')
            WHEN '' THEN ''
            WHEN 1 THEN 'Passed'
            WHEN 2 THEN 'Failed'
            END
            ";
        $T2PassSubquery = "CASE 
            COALESCE((
                SELECT MIN(files_qcstatus.QCStatus+0) 
                FROM files JOIN files_qcstatus 
                USING (FileID) 
                WHERE files.SessionID=s.ID AND files.AcquisitionProtocolID=45 and (files_qcstatus.QCStatus=1 or files_qcstatus.QCStatus=2)
                GROUP BY files.SessionID), '')
            WHEN '' THEN ''
            WHEN 1 THEN 'Passed'
            WHEN 2 THEN 'Failed'
            END
            ";

        $PendingFailSubquery = "
            CASE s.MRIQCStatus
                WHEN 'Fail' THEN
                    IF(s.MRIQCPending='Y', 'Pending Fail', 'Fail')
                WHEN 'Pass' THEN
                    IF(s.MRIQCPending='Y', 'Pending Pass', 'Pass')
                ELSE s.MRIQCStatus
            END 
            ";

        $this->query = " FROM psc AS p 
            JOIN session s ON (s.CenterID=p.CenterID) 
            JOIN candidate c ON (c.CandID=s.CandID) 
            JOIN files f ON (f.SessionID=s.ID) 
            LEFT JOIN files_qcstatus fqc ON (fqc.FileID=f.FileID) 
            JOIN mri_acquisition_dates md ON (md.SessionID=s.ID)
            WHERE 
            f.PendingStaging=0 AND 
            f.FileType='mnc' AND 
            f.AcquisitionProtocolID not in (1, 2, 3, 52)";

        $user =& User::singleton();
        $DB = Database::singleton();
        if (!$user->hasPermission('imaging_browser_view_allsites')) {
            $this->query .= " AND c.CenterID=" . $DB->quote($user->getCenterID());
        }

        $this->columns = array(
            'p.MRI_alias as Site',
            'c.PSCID as PSCID',
            'c.CandID as DCCID',
            's.visit_label as Visit_Label',
            "$PendingFailSubquery as QC_Status",
            'MIN(md.AcquisitionDate) as First_Acq_Date',
            'FROM_UNIXTIME(MAX(fqc.QCLastChangeTime)) as Last_QC',
            "$NewDataSubquery as New_Data",
            "$T1PassSubquery as T1_Passed",
            "$T2PassSubquery as T2_Passed",
            "GROUP_CONCAT(DISTINCT OutputType) as Links",
            's.ID as sessionID'
        );
        $this->order_by = 'c.PSCID, s.Visit_label';
        $this->group_by = 's.ID';
        $this->headers = array(
            'Site',
            'PSCID',
            'DCCID',
            'Visit_Label',
            'QC_Status', 
            'First_Acq_Date',
            'Last_QC',
            'New_Data',
            'T1_Passed',
            'T2_Passed',
            'Links',
            'sessionID'
        );
       
        $this->validFilters = array(
            'c.PSCID',
            's.Visit_label',
            'c.CandID',
            'c.ProjectID',
            'c.CenterID',
            's.MRIQCStatus',
            'pending',
            'f.AcquisitionProtocolID'
        );

        $this->formToFilter = array(
            'pscid' => 'c.PSCID',
            'VL'    => 's.Visit_label',
            'DCCID'=> 'c.CandID',
            'ProjectID' => 'c.ProjectID',
            'SiteID' => 'c.CenterID',
            'VisitQCStatus' => 's.MRIQCStatus',
            'Pending' => 'pending',
            'Scan_type'=>'f.AcquisitionProtocolID'
        );
        $this->EqualityFilters = array('f.AcquisitionProtocolID');
        $this->searchKeyword    = array();
        
        $this->tpl_data['numTimepoints'] = 0;

        // This variable will be used by the columnFormatter javascript
        // to set the default hidden columns in the data table.
        $this->tpl_data['hiddenHeaders'] = json_encode(
            array_map(
                function ($header) {
                        return ucwords(str_replace('_', ' ', $header));
                },
                array('sessionID')
            )
        );
    }

    /**
     * Setup $this->tpl_data for use by Smarty
     *
     * @return null
     */
    function _setFilterForm()
    {
        // create user object
        $user =& User::singleton();
        
        // PSC
        if ($user->hasPermission('imaging_browser_view_allsites')) {
            // get the list of study sites - to be replaced by the Site object
            $list_of_sites = Utility::getSiteList();
            if(is_array($list_of_sites)) $list_of_sites = array('' => 'All') + $list_of_sites;
        }
        else {
            // allow only to view own site data
            $site =& Site::singleton($user->getData('CenterID'));
            if ($site->isStudySite()) {
                $list_of_sites = array($user->getData('CenterID') => $user->getData('Site'));
            }
        }

        $DB = Database::singleton();
        $allAr = array('' => 'All');
        $this->addBasicText('pscid', 'PSCID', array("size"=>10,"maxlength"=>25));
        $this->addBasicText(
            'VL', 'Visit Label', array('size' => "10", "maxlength"=>"25")
        );
        $this->addBasicText(
            'DCCID', 'DCCID', array('size' => 10, "maxlength" => 25)
        );

        $config =& NDB_Config::singleton();
        if ($config->getSetting('useProjects') === "true") {
            $list_of_projects = $allAr;
            $projectList = Utility::getProjectList();
            foreach ($projectList as $key => $value) {
                $list_of_projects[$key] =$value;
            }
            $this->addSelect('ProjectID', 'Project', $list_of_projects);  
        }

        $this->addSelect('SiteID', 'Site', $list_of_sites);
        $this->addSelect(
            'VisitQCStatus',
            'QC Status',
            array(''=>'All', 'Pass'=>'Pass', 'Fail'=>'Fail')
        );

        $this->addSelect(
            'Pending',
            'Pending and New',
            array(''=>'All', 'P'=>'Pending', 'N'=>'New', 'PN'=>'Pending or New')
        );

        //query to get scan types that exist for the project
        $types_q = $DB->pselect(
                "SELECT ID, Scan_type FROM mri_scan_type mri
                 JOIN files f ON (f.AcquisitionProtocolID=mri.ID)",
                 array()
        );
        $scan_types = $allAr;
        foreach ($types_q as $row) {
            $type                   = $row['Scan_type'];
            $scan_types[$row['ID']] = $type;
        }
        $this->addSelect('Scan_type', 'Sequence Type', $scan_types);

        $outputTypes = $DB->pselect(
            "SELECT DISTINCT OutputType AS outputType 
            FROM files WHERE FileType='mnc' AND OutputType!='native'", array()
        );

        $this->tpl_data['outputTypes'] = array_merge(
            array(
                array('outputType'=>'native'),array('outputType'=>'selected')
            ),
            $outputTypes
        );

        $this->tpl_data['numOutputTypes'] = count($outputTypes);
        $this->addBasicText(
            'keyword', 
            'Search keyword in Comments', 
            array("size"=>10,"maxlength"=>25)
        );

        $this->tpl_data['backURL'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Overwrites the function to add a customized filter
     * for Pending and New
     *
     * @param string $prepared_key filter key
     * @param string $field        filter field
     * @param string $val          filter value
     *
     * @return null
     */
    function _addValidFilters($prepared_key, $field, $val)
    {
        $query = '';
        if ((!empty($val) || $val === '0') && $field != 'order') {
            if ($field != 'pending' && $field !='AcquisitionProtocolID') {
                if (in_array($field, $this->CheckboxFilters) && $val) {
                    $query .= " AND $field";
                } elseif (strtolower(substr($field, -8)) == 'centerid'
                        || strtolower(substr($field, -10)) == 'categoryid'
                        || strtolower(substr($field, -6)) == 'gender'
                        || (isset($this->EqualityFilters)
                        && in_array($field, $this->EqualityFilters))
                        ) {
                    $query .= " AND $field = :v_$prepared_key";
                    // $qparams["v_$prepared_key"] = $val;
                } else {
                    $query .= " AND $field LIKE CONCAT('%', :v_$prepared_key, '%') ";
                }
            } else if ($field == 'pending') {
                switch ($val) {
                case "P":
                    $query .= " AND s.MRIQCPending='Y'"; 
                    break;
                case "N":
                    $query .= " AND fqc.QCFirstChangeTime IS NULL"; 
                    break;
                case "PN":
                    $query .= " AND (s.MRIQCPending='Y' 
                        OR fqc.QCFirstChangeTime IS NULL)"; 
                    break;
                }
            } else if ($field == 'AcquisitionProtocolID') {
                  $query .= " AND $field = :v_$prepared_key";
            }
        }
        return $query;
    }

    /**
     * Converts this menu filter to an array of the form
     *
     * Headers => (string array)
     * Data => (array of arrays of row data)
     *
     * @note overloaded function
     *         Overloading this method to create a list of sessionID that
     *         will be used for the Navigation Links in  the viewSession
     *         page.
     *
     * @return associative array
     */
    function toArray()
    {
        $data   = parent::toArray();
        $index = array_search('SessionID', $data['Headers']);

// temporary until php is upgraded
if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}

        if ($index !== false) {
            $_SESSION['State']->setProperty(
                'mriSessionsListed',
                array_column($data['Data'], $index)
            );
        }
        return $data;
    }

    /**
     * Include the column formatter
     *
     * @return array of javascript to be inserted
     */
    function getJSDependencies() {
        $factory = NDB_Factory::singleton();
        $baseurl = $factory->settings()->getBaseURL();
        return array_merge(
            parent::getJSDependencies(),
            array(
                $baseurl . "/imaging_browser/js/columnFormatter.js"
            )
        );
    }
}
