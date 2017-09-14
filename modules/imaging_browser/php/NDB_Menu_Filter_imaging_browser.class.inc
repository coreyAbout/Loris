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
 *  @license    Loris License
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
 *  @license    Loris License
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
        $site_arr = $user->getData('CenterIDs');
        foreach ($site_arr as $key=>$val) {
            $site[$key]        = & Site::singleton($val);
            $isStudySite[$key] = $site[$key]->isStudySite();
        }
        $oneIsStudySite = in_array("1", $isStudySite);

        return ($user->hasPermission('imaging_browser_view_allsites')
               || ($oneIsStudySite
                   && $user->hasPermission('imaging_browser_view_site')
                  )
               || $user->hasPermission('imaging_browser_phantom_allsites')
               || $user->hasPermission('imaging_browser_phantom_ownsite')
        );
    }

    /**
     * Set up the variables required by NDB_Menu_Filter class for constructing
     * a query
     *
     * @return null
     */
    function _setupVariables()
    {
        $config = NDB_Config::singleton();
        $DB     = Database::singleton();
        $toTable_scan_types = $config->getSetting('tblScanTypes');

        $all_scan_types_2d = $DB->pselect(
            "SELECT ID, Scan_type FROM mri_scan_type mri",
            array()
        );

        foreach ($all_scan_types_2d as $row) {
            $type = $row['Scan_type'];
            $all_scan_types[$row['ID']] = $type;
        }

        // Get the intersection between all the scan types and those
        // that are desired to go into imaging browser table, based on
        // array values rather than keys (hence the array_flip), then flip
        // the resulting array back to revert it to a key/value (i.e.
        // acquisition protocol ID/scan type) combination.
        $scan_id_types =array_flip(
            array_intersect_key(
                array_flip($all_scan_types),
                array_flip($toTable_scan_types)
            )
        );

        if (!empty($scan_id_types)) {
            $this->acqpid = "AND AcquisitionProtocolID IN (".
                    implode(",", array_keys($scan_id_types)) .")";
            $isFirst      = true;
            foreach ($scan_id_types as $key => $value) {
                if ($isFirst) {
                    $acqpif = "IF(FIND_IN_SET({$key},GROUP_CONCAT(
                        DISTINCT AcquisitionProtocolID))>0 ";
                } else {
                    $acqpif .= "OR FIND_IN_SET({$key},GROUP_CONCAT(
                        DISTINCT AcquisitionProtocolID))>0 ";
                }
                $isFirst = false;
            }
            $acqpif .= ",'new','')";
        } else {
            $this->acqpid = '';
            $acqpif       = "'new'";
        }

        $NewDataSubquery = "CASE 
            COALESCE(Max(fqc.QCLastChangeTime), 'new')
            WHEN 'new' THEN {$acqpif}
            WHEN ''    THEN {$acqpif}
            ELSE ''
            END";

        foreach ($scan_id_types as $id => $type) {
            $pass[$id]          = $type . 'pass';
            $qc[$id]            = $type . 'QC';
            $coalesce_desc[$id] = $pass[$id] . '.' . $qc[$id];
            $case_desc[$id]     = "CASE
                COALESCE($coalesce_desc[$id], '')
                WHEN '' THEN ''
                WHEN 1 THEN 'Passed'
                WHEN 2 THEN 'Failed'
                END";
        }

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
            JOIN mri_acquisition_dates md ON (md.SessionID=s.ID)";

        $left_joins = "";
        foreach ($case_desc as $key => $value) {
            $left_joins .= "
            LEFT JOIN (SELECT files.SessionID, 
            MIN(files_qcstatus.QCStatus+0) as " . $DB->escape($qc[$key]) . " 
            FROM files 
            JOIN files_qcstatus USING (FileID) 
            WHERE files.AcquisitionProtocolID=" . $DB->quote($key) . " 
            AND files_qcstatus.QCStatus IN (1, 2) 
            GROUP BY files.SessionID) " . $DB->escape($pass[$key]) . "
            ON (" . $DB->escape($pass[$key]) . ".SessionID=f.SessionID) ";
        }

        $where = "
            WHERE 
            s.Active = 'Y' AND
            f.PendingStaging=0 AND 
            f.FileType='mnc' AND 
            f.AcquisitionProtocolID not in (1, 2, 3, 52) AND 
            f.OutputType='native'";

        $this->query .= $left_joins;
        $this->query .= $where;

        $config =& NDB_Config::singleton();
        $user   =& User::singleton();
        $DB     = Database::singleton();
        if (!$user->hasPermission('imaging_browser_view_allsites')) {
            $site_arr = implode(",", $user->getCenterIDs());
            if (!$user->hasPermission('imaging_browser_view_site')) {
                // User must have one of the two phantom permissions to get here
                $this->query .= " AND c.Entity_type = 'Scanner' ";
                if (!$user->hasPermission('imaging_browser_phantom_allsites')) {
                    // Display only the phantom scans at the user's centers
                    // For phantoms, use session centerID
                    $this->query .= " AND s.CenterID IN (" . $site_arr . ") ";
                }
            } else {
                if ($user->hasPermission('imaging_browser_phantom_allsites')) {
                    $this->query .= " AND (c.CenterID IN (" . $site_arr . ") 
                                      OR c.Entity_type = 'Scanner') ";
                } else if ($user->hasPermission('imaging_browser_phantom_ownsite')) {
                    $this->query .= " AND (c.CenterID IN (" . $site_arr . ")
                                      OR (c.Entity_type = 'Scanner'
                                      AND s.CenterID IN (" . $site_arr . "))) ";
                } else {
                    $this->query .= " AND c.CenterID IN (" . $site_arr . ") ";
                }
            }
        }

        // Initialize the arrays that go into this->columns and
        // $this->headers so that array_merge works even if no
        // $scan_types are set in the configuration module
        $modalities_subquery_as = array();
        $as = array();

        foreach ($case_desc as $key => $value) {
            $modalities_subquery[$key] = $value;
            $as[$key] = $scan_id_types[$key] . '_QC_Status';
            $modalities_subquery_as[$key] = $modalities_subquery[$key] .
                                            ' as ' .
                                            $as[$key];
        }

        $this->columns = array(
                          'p.MRI_alias as Site',
                          'c.PSCID as PSCID',
                          'c.CandID as DCCID',
                          's.visit_label as Visit_Label',
                          "$PendingFailSubquery as Visit_QC_Status",
                          'MIN(md.AcquisitionDate) as First_Acquisition',
                          'FROM_UNIXTIME(MIN(f.InsertTime)) as First_Insertion',
                          'FROM_UNIXTIME(MAX(fqc.QCLastChangeTime)) as Last_QC',
                          "$NewDataSubquery as New_Data",
                         );
        $this->columns = array_merge(
            $this->columns,
            array("GROUP_CONCAT(DISTINCT OutputType) as Links"),
            $modalities_subquery_as,
            array('s.ID as sessionID')
        );

        $this->order_by = 'c.PSCID, s.Visit_label';
        $this->group_by = 's.ID';

        $this->headers = array(
                          'Site',
                          'PSCID',
                          'DCCID',
                          'Visit_Label',
                          'Visit_QC_Status',
                          'First_Acquisition',
                          'First_Insertion',
                          'Last_QC',
                          'New_Data',
                          'Links',
                         );
        $this->headers = array_merge(
            $this->headers,
            $as,
            array('sessionID')
        );

        // Insert project column after DCCID, if useProject config is enabled
        if ($config->getSetting('useProjects') === "true") {
            array_splice(
                $this->columns,
                3,
                0,
                '(SELECT Name FROM Project WHERE ProjectID=c.ProjectID) as project'
            );
            array_splice($this->headers, 3, 0, 'Project');
        }

        $this->validFilters = array(
                               'c.PSCID',
                               's.Visit_label',
                               'c.CandID',
                               'c.ProjectID',
                               's.CenterID',
                               's.MRIQCStatus',
                               'pending',
                               'f.AcquisitionProtocolID',
                              );

        $this->formToFilter    = array(
                                  'pscid'         => 'c.PSCID',
                                  'VL'            => 's.Visit_label',
                                  'DCCID'         => 'c.CandID',
                                  'ProjectID'     => 'c.ProjectID',
                                  'SiteID'        => 's.CenterID',
                                  'VisitQCStatus' => 's.MRIQCStatus',
                                  'Pending'       => 'pending',
                                  'Scan_type'     => 'f.AcquisitionProtocolID',
                                 );
        $this->EqualityFilters = array('f.AcquisitionProtocolID');
        $this->searchKeyword   = array();

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
        $user          =& User::singleton();
        $list_of_sites = array();

        // PSC
        if ($user->hasPermission('imaging_browser_view_allsites')
            || $user->hasPermission('imaging_browser_phantom_allsites')
        ) {
            // get the list of study sites - to be replaced by the Site object
            $list_of_sites = Utility::getSiteList();
            if (is_array($list_of_sites)) {
                $list_of_sites = array('' => 'All') + $list_of_sites;
            }
        } else {
            // allow only to view own site data
            $site_arr = $user->getData('CenterIDs');
            foreach ($site_arr as $key => $val) {
                $site[$key] = &Site::singleton($val);
                if ($site[$key]->isStudySite()) {
                    $list_of_sites[$val] = $site[$key]->getCenterName();
                }
                $list_of_sites = array('' => 'All User Sites') + $list_of_sites;
            }
        }

        $DB    = Database::singleton();
        $allAr = array('' => 'All');

        $this->addBasicText(
            'pscid',
            'PSCID',
            array(
             "size"      => 10,
             "maxlength" => 25,
            )
        );
        $this->addBasicText(
            'VL',
            'Visit Label',
            array(
             'size'      => "10",
             "maxlength" => "25",
            )
        );
        $this->addBasicText(
            'DCCID',
            'DCCID',
            array(
             'size'      => 10,
             "maxlength" => 25,
            )
        );

        $config =& NDB_Config::singleton();
        if ($config->getSetting('useProjects') === "true") {
            $list_of_projects = $allAr;
            $projectList      = Utility::getProjectList();
            foreach ($projectList as $key => $value) {
                $list_of_projects[$key] =$value;
            }
            $this->addSelect('ProjectID', 'Project', $list_of_projects);
        }

        $this->addSelect('SiteID', 'Site', $list_of_sites);
        $this->addSelect(
            'VisitQCStatus',
            'Visit QC Status',
            array(
             ''     => 'All',
             'Pass' => 'Pass',
             'Fail' => 'Fail',
            )
        );

        $this->addSelect(
            'Pending',
            'Pending and New',
            array(
             ''   => 'All',
             'P'  => 'Pending',
             'N'  => 'New',
             'PN' => 'Pending or New',
            )
        );

        //query to get scan types that exist for the project
        $types_q    = $DB->pselect(
            "SELECT ID, Scan_type FROM mri_scan_type mri
                 JOIN files f ON (f.AcquisitionProtocolID=mri.ID)",
            array()
        );
        $scan_types = $allAr;
        foreach ($types_q as $row) {
            $type = $row['Scan_type'];
            $scan_types[$row['ID']] = $type;
        }
        $this->addSelect('Scan_type', 'Sequence Type', $scan_types);

        $outputTypes = $DB->pselect(
            "SELECT DISTINCT OutputType AS outputType 
            FROM files WHERE FileType='mnc' AND OutputType!='native'",
            array()
        );

        $this->tpl_data['outputTypes'] = array_merge(
            array(
             array('outputType' => 'native'),
             array('outputType' => 'selected'),
            ),
            $outputTypes
        );

        $this->tpl_data['numOutputTypes'] = count($outputTypes);
        $this->addBasicText(
            'keyword',
            'Search keyword in Comments',
            array(
             "size"      => 10,
             "maxlength" => 25,
            )
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
                    $query .= " AND fqc.QCFirstChangeTime IS NULL " . $this->acqpid;
                    break;
                case "PN":
                    $query .= " AND (s.MRIQCPending='Y' 
                        OR (fqc.QCFirstChangeTime IS NULL ". $this->acqpid ."))";
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
        $data  = parent::toArray();
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
     * Include additional CSS files:
     *  1. imaging_browser.css
     *
     * @return array of css to be inserted
     */
    function getCSSDependencies()
    {
        $factory = NDB_Factory::singleton();
        $baseURL = $factory->settings()->getBaseURL();
        $deps    = parent::getCSSDependencies();
        return array_merge(
            $deps,
            [$baseURL . "/imaging_browser/css/imaging_browser.css"]
        );
    }


    /**
     * Include the column formatter
     *
     * @return array of javascript to be inserted
     */
    function getJSDependencies()
    {
        $factory = NDB_Factory::singleton();
        $baseurl = $factory->settings()->getBaseURL();
        return array_merge(
            parent::getJSDependencies(),
            array(
             $baseurl . "/imaging_browser/js/columnFormatter.js",
             $baseurl . "/imaging_browser/js/onSort.js",
            )
        );
    }
}
