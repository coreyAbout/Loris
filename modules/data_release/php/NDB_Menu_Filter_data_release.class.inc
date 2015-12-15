<?php
/**
 * This file handles the Data Release for LORIS
 *
 * PHP Version 5
 *
 * @category LORIS
 * @package  Main
 * @author   Justin Kat <justinkat@gmail.com>
 * @license  GPLv3
 * @link     https://www.github.com/aces/Loris
 */

require_once 'NDB_Menu_Filter.class.inc';

/**
 * Data Release Class
 *
 * This class is for the Data Release
 * 
 * @category LORIS
 * @package  Main 
 * @author   Justin Kat <justinkat@gmail.com>
 * @license  GPLv3
 * @link     https://www.github.com/aces/Loris
*/

class NDB_Menu_Filter_Data_Release extends NDB_Menu_Filter
{

    function _setupVariables()
    {
        $user =& User::singleton();

        // set the class variables
        $this->columns = array('file_name','version','upload_date');
        $this->query = " FROM data_release WHERE id IN (SELECT data_release_id FROM data_release_permissions WHERE userid=(SELECT ID FROM users WHERE UserID='{$user->getUsername()}'))";
        $this->group_by = '';
        $this->order_by = 'upload_date';
        $this->headers = array('file_name', 'version', 'upload_date');
        $this->validFilters = array('file_name','version','upload_date');
        $this->formToFilter = array(
                                    'file_name' => 'file_name',
                                    'version' => 'version',
                                    'upload_date' => 'upload_date'
                                    );
        return true;
    }

}

?>
