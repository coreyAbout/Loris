<?php

/**
 * Add permissions through Ajax, how crazy.
 *
 * PHP Version 5
 *
 *  @category Loris
 *  @package  Data_Release
 *  @author   Justin Kat <justinkat@gmail.com>
 *  @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 *  @link     https://github.com/aces/Loris
 */

$DB =& Database::singleton();

if ($_POST['action'] == 'addpermission') {
    if (!empty($_POST['data_release_id']) && empty($_POST['data_release_version'])) {
        $userid          = $_POST['userid'];
        $data_release_id = $_POST['data_release_id'];
        $success         = $DB->insert(
            'data_release_permissions',
            array(
             'userid'          => $userid,
             'data_release_id' => $data_release_id,
            )
        );
    } elseif (empty($_POST['data_release_id']) && !empty($_POST['data_release_version'])) {
        $userid               = $_POST['userid'];
        $data_release_version = $_POST['data_release_version'];

        $IDs      = $DB->pselect(
            "SELECT id FROM data_release WHERE "
            . "version=:data_release_version",
            array(
             'data_release_version' => $data_release_version,
            )
        );

        foreach ($IDs as $ID) {
            $success         = $DB->insert(
             'data_release_permissions',
             array(
              'userid'          => $userid,
              'data_release_id' => $ID['id'],
             )
            );
        }
    }

    $factory  = NDB_Factory::singleton();
    $settings = $factory->settings();

    $baseURL = $settings->getBaseURL();

    header("Location: {$baseURL}/data_release/?addpermissionSuccess=true");
} else {
    header("HTTP/1.1 400 Bad Request");
    echo "There was an error adding permissions";
}

?>
