<?php

require_once "../vendor/autoload.php";

$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize('../project/config.xml');

$DB =& Database::singleton();

$count = 0;

if ($argv[1] == 'local') {

    $picture = file('./picture');
    $pictures = array();
    foreach ($picture as $line) {
        $pictures[] = str_getcsv($line);
    }

    $semantic = file('./semantic');
    $semantics = array();
    foreach ($semantic as $line) {
        $semantics[] = str_getcsv($line);
    }

    $version = file('./version');
    $versions = array();
    foreach ($version as $k => $v) {
        $versions[$k] = str_getcsv($v);
    }

    $age = file('./age');
    $ages = array();
    foreach ($age as $line) {
        $ages[] = str_getcsv($line);
    }

    $language = file('./language');
    $languages = array();
    foreach ($language as $line) {
        $languages[] = str_getcsv($line);
    }

    for ($i = 1; $i <= 783; $i++) {

        $candidate_age = floor(($ages[$i][0])/12);
        $age_range = "";
        if ($candidate_age >= 50 && $candidate_age <= 59) {
            $age_range = "50_59";
        }
        elseif($candidate_age >= 60 && $candidate_age <= 69) {
            $age_range = "60_69";
        }
        elseif($candidate_age >= 70 && $candidate_age <= 79) {
            $age_range = "70_79";
        }
        elseif($candidate_age >=80 && $candidate_age <= 89) {
            $age_range = "80_89";
        }

        if ($age_range == "") {
            echo "Row " . $i . "+1 has no age range because candidate age is: " . $candidate_age . "\n";
        }

        echo "Checking results for Row " . $i . "+1 with version: " . $versions[$i][0] . " age: " . $candidate_age . " range: " . $age_range . " picture: " . $pictures[$i][0] . " semantic: " . $semantics[$i][0] . "\n";

        $lookup_language_score = $DB->pselectOne("select score from language_index_scores_$age_range where picture_naming_ts ='" . $pictures[$i][0] . "' AND semantic_fluency_ts ='". $semantics[$i][0] . "'",null);

        if ($lookup_language_score != $languages[$i][0]) {
            echo "Row " . $i . "+1 with version: " . $versions[$i][0] . " lookup score: " . $lookup_language_score . " but stored score was " . $languages[$i][0] . "\n";
            $count = $count + 1;
        }

    }

    echo "Total incorrect results: " . $count . "\n";

} elseif ($argv[1] == 'database') {

    $RBANS = $DB->pselect("select * from RBANS where commentid not like '%MTL0000%' and commentid not like '%MTL999%'",array());
    foreach ($RBANS as $R) {
        $candidate_age = floor(($R['Candidate_Age'])/12);
        $age_range = "";
        if ($candidate_age >= 50 && $candidate_age <= 59) {
            $age_range = "50_59";
        }
        elseif($candidate_age >= 60 && $candidate_age <= 69) {
            $age_range = "60_69";
        }
        elseif($candidate_age >= 70 && $candidate_age <= 79) {
            $age_range = "70_79";
        }
        elseif($candidate_age >=80 && $candidate_age <= 89) {
            $age_range = "80_89";
        }
 
        if ($age_range == "") {
            echo "CommentID " . $R['CommentID'] . " has no age range because candidate age is: " . $candidate_age . "\n";
        }

        $lookup_language_score = $DB->pselectOne("select score from language_index_scores_$age_range where picture_naming_ts ='" . $R['picture_naming_score'] . "' AND semantic_fluency_ts ='". $R['semantic_fluency_score'] . "'",null);
        $stored_language_score = $R['language_index_score'];

        if ($lookup_language_score != $stored_language_score) {
            echo "CommentID: " . $R['CommentID'] . " lookup score: " . $lookup_language_score . " stored score: " . $stored_language_score . "\n";
            $count = $count + 1;
        }
        
    }

    echo "Total incorrect results: " . $count . "\n";

}

?>
