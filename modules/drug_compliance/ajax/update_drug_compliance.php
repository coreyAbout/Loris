<?php

ini_set('default_charset', 'utf-8');

/**
 *
 * This is used by the Drug Compliance page to update the fields on the fly
 *
**/

require_once "Database.class.inc";
require_once 'NDB_Config.class.inc';
require_once 'NDB_Client.class.inc';
$config =& NDB_Config::singleton();
$client = new NDB_Client();
$client->initialize();
require_once 'NDB_Form_drug_compliance.class.inc';

if (get_magic_quotes_gpc()) {
    // Magic quotes adds \ to description, get rid of it.
    $id = stripslashes($_REQUEST['id']);
    $field = stripslashes($_REQUEST['field']);
    $value = stripslashes($_REQUEST['value']);
} else {
    // Magic quotes is off, so we can just directly use the description
    // since insert() will use a prepared statement.
    $id = $_REQUEST['id'];
    $field = $_REQUEST['field'];
    $value = $_REQUEST['value'];
}

// create user object
$user =& User::singleton();
if (Utility::isErrorX($user)) {
    return PEAR::raiseError("User Error: ".$user->getMessage());
}

if ($user->hasPermission('data_entry')) { //if user has data entry permission
    //only allow certain fields to be updated
    if ($field == 'drug_issued_date' || $field == 'pills_issued' || $field == 'drug_returned_date' || $field == 'pills_returned') {
        $DB->update('drug_compliance', array($field=>$value), array('ID'=>$id));
        $candid = $DB->pselectOne("SELECT candid from drug_compliance where ID=:dcid", array('dcid'=>$id));
        NDB_Form_drug_compliance::calculate($candid, $id);
    }
}

?>
