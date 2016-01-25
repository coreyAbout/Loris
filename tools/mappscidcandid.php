<?php
/*
script to map PSCID to CandID
*/

require_once "/var/www/neurodb/vendor/autoload.php";
// load the client
set_include_path(get_include_path().":/var/www/neurodb/project/libraries:var/www/neurodb/php/libraries:");
ini_set('default_charset', 'utf-8');

require_once "PEAR.php";
require_once 'NDB_Client.class.inc';

$client = new NDB_Client;
$client->makeCommandLine();
$client->initialize("/var/www/neurodb/project/config.xml");



$fixedLines = file("/tmp/pscid.csv");
$fields = array();
$thisField = array();

$counter = 0;
$id = 0;
$PSCID = "";

for( $i = 0; $i < sizeof($fixedLines); $i++ )
{
	if ($i == 0)
	{
		$key = explode(",", $fixedLines[$i]);

		for ($j = 0; $j < sizeof($key); $j++)
		{
			$field = $key[$j];
			$thisField[$field] = "";
		}
	}

	else
	{
		$fieldNum = 0;
		$data = explode(",", $fixedLines[$i]);

                                $counter ++;
//                                echo "-------count: " . $counter ."\n";
				$PSCID = str_replace("\n","",$data[0]);
//				echo "-------PSCID: " . $PSCID . "\n";

		$config =& NDB_Config::singleton();
		$db =& Database::singleton();
		if(Utility::isErrorX($db)) {
			fwrite(STDERR, "Could not connect to database: ".$db->getMessage());
			return false;
		}

$success = $db->pselectOne("SELECT candid FROM candidate WHERE PSCID=:pid",array("pid"=>$PSCID));
echo $success . "\n";
			if (Utility::isErrorX($success)) {
				fwrite(STDERR, "Failed, DB Error: " . $success->getMessage()."\n");
				return false;
			}
//			echo "Updated!\n";
	}
}

?>
