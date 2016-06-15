<?php

set_include_path(get_include_path().":../project/libraries:../php/libraries:../modules/drug_compliance/php:");
require_once __DIR__ . "/../vendor/autoload.php";
require_once "NDB_Client.class.inc";
$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize("../project/config.xml");

require_once "NDB_Form_drug_compliance.class.inc";
NDB_Form_drug_compliance::calculate();

?>
