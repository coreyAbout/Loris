<?php
/**
 * Purpose:
 * This script should be used by projects with existing data in the parameter_type,
 * parameter_type_category_rel and parameter_type_category tables. This script should
 * be run before using the data_dictionary_builder.php on loris version 21.0.0 +.
 *
 * This script flags potential issues in the instruments' data dictionary stored in
 * the parameter_type, parameter_type_category_rel and parameter_type_category tables
 *
 * Issues flag in this script:
 *  - Multiple/Orphan entries per instrument in the parameter_type_category table.
 *  - Non-unique `Name` values for instrument fields.
 *  - Empty `Name` values for instrument fields.
 *
 * PHP Version 7
 *
 * @category Behavioural
 * @package  Main
 * @author   Loris Team <loris.mni@bic.mni.mcgill.ca>
 * @license  Loris License
 * @link     https://github.com/aces/Loris
 */
require_once __DIR__ . '/../generic_includes.php';

const NUM_ARGS_REQUIRED = 2;

echo "
#####################################################################################
This script checks the parameter_type, parameter_type_category_rel and 
parameter_type_category tables of the database for duplicate entries.
This script offers the option to remove duplication directly or through a 
generated SQL patch.
#####################################################################################
\n\n";

// define the command line parameters
// expected commands all have 2 parameters
if (count($argv) != NUM_ARGS_REQUIRED || $argv[1] == "help") {
    showHelp();
}

$filePath = __DIR__ . "/../../SQL/Archive/autogenerated/single_use/data_dictionary_cleaner.sql";

$apply      = false;
$printToSQL = false;

if ($argv[1] === "apply") {
    $apply = true;
} elseif ($argv[1] === "tosql") {
    $printToSQL = true;
} elseif ($argv[1] !== "check") {
    // If the argument is anything else than "check" show help
    showHelp();
}

$dupFields = $DB->pselectWithIndexKey(
    "SELECT pt.ParameterTypeID, pt.Name, dr.maxID as DuplicateOf
    FROM parameter_type pt
    JOIN (
    	SELECT MAX(pt.ParameterTypeID) as maxID, pt.Name as fieldName, COUNT(*) as duplicationCount 
    	FROM parameter_type_category ptc
			JOIN parameter_type_category_rel ptcr USING(ParameterTypeCategoryID) 
			JOIN parameter_type pt USING(ParameterTypeID) 
    	WHERE ptc.Type='Instrument'
    	GROUP BY fieldName
    	HAVING duplicationCount>1) as dr ON (dr.fieldName=pt.Name)
    WHERE pt.ParameterTypeID < dr.maxID",
    array(),
    "ParameterTypeID"
);

$emptyFields = $DB->pselectCol(
    "SELECT pt.ParameterTypeID
    FROM parameter_type pt
      JOIN parameter_type_category_rel ptcr USING(ParameterTypeID) 
	  JOIN parameter_type_category ptc USING(ParameterTypeCategoryID)
	WHERE (pt.Name IS NULL OR pt.Name='')
	  AND pt.ParameterTypeID IS NOT NULL
	  AND ptc.Type='Instrument'",
    array()
);

$orphanCategories = $DB->pselect(
    "SELECT ptc.ParameterTypeCategoryID, ptc.Name as instrumentName 
    FROM parameter_type_category ptc
		LEFT JOIN parameter_type_category_rel ptcr USING(ParameterTypeCategoryID) 
		LEFT JOIN parameter_type pt USING(ParameterTypeID) 
    WHERE ptc.Type='Instrument' AND pt.ParameterTypeID IS NULL",
    array()
);


// CHECK FOR ISSUES AND REPORT THEM
if (empty($dupFields) && empty($emptyFields) && empty($orphanCategories)) {
    echo "No issues have been detected in the database.\n\n";
    die();
}
// Handle case where a `Name` entry in the parameter_type table is not unique.
// The `Name` is a concatenation of the instrument with the field name. A non-unique
// value can be caused by several factors including but not limited to:
// - A field in the instrument being declared more than once
if (!empty($dupFields)) {
    echo "PARAMETER TYPE ENTRIES (instrument fields)
    The following entries are duplicated. Each duplicated entry below is listed along 
    with it's ID, name and the ID of the homologue entry that will be preserved. 
    Duplicated fields can be caused by several factors such as:
     - Failed attempt at clearing the fields before rebuilding the dictionary.
     - A duplicated field in an instrument.
     - other...
    Duplicated entries should be removed in order for the data_dictionary_builder.php
    script to run on LORIS versions 21.0.0 and above. Removal of duplicated fields 
    should be handled with care to avoid creating orphan entries in database tables 
    with a foreign key reference to the parameterTypeID field of the parameter_type table.\n\n";

    print_r($dupFields);
}

// Handle case where the parameter_type table has fields with an empty `Name` value.
// The database column should not be nullable however empty names are possible and
// thus should be detected and eliminated as they are not informative in any way.
if (!empty($emptyFields)) {
    echo "The following entries in the parameter_type table do not have a `Name` 
    value and are thus invalid. these entries can be safely removed.\n\n";
    print_r($emptyFields);
}

// Handle case where the parameter_type_category table has orphans for instruments.
// Instrument names get stored as a "category" in the table. Due to a previous bug
// with the builder, this table was not being cleared before being re-populated.
// Old entries have IDs that are not linked to any values in the parameter_type table
// and should be removed entirely.
if (!empty($orphanCategories)) {
    echo "The following entries in the parameter_type_category table are not linked 
    to any data in the parameter_type table. These entries can be safely removed 
    without affecting the data dictionary.\n\n";
    print_r($orphanCategories);
}

// CHECK FOR ACTION AND APPLY THE NECESSARY CHANGES
if (!$apply && !$printToSQL) {
    echo "This script can automatically clean the parameter_type and parameter_type_category tables;\n\n";
    showHelp();
} elseif ($apply) {
    // run changes in database
    // Fields first since they might generate orphan category entries.
    // delete the parameter_type and parameter_type_category_rel entries
    echo "#### CLEARING DUPLICATE PARAMETER TYPE ENTRIES ####\n";
    foreach ($dupFields as $pti => $fieldData) {
        $DB->delete(
            "parameter_type_category_rel",
            array("ParameterTypeID" => $pti)
        );
        $DB->delete(
            "parameter_type",
            array("ParameterTypeID" => $pti)
        );
    }

    // Empty Fields next since they might generate orphan category entries.
    // delete the parameter_type and parameter_type_category_rel entries
    echo "#### CLEARING NAMELESS PARAMETER TYPE ENTRIES ####\n";
    foreach ($emptyFields as $pti) {
        $DB->delete(
            "parameter_type_category_rel",
            array("ParameterTypeID" => $pti)
        );
        $DB->delete(
            "parameter_type",
            array("ParameterTypeID" => $pti)
        );
    }

    // Single query for orphan categories to clean-up the tables
    echo "#### CLEARING ORPHAN PARAMETER TYPE CATEGORY ENTRIES ####\n";
    $DB->run(
        "DELETE ptc
      FROM parameter_type_category ptc
		LEFT JOIN parameter_type_category_rel ptcr USING(ParameterTypeCategoryID)
	    LEFT JOIN parameter_type pt USING(ParameterTypeID)
      WHERE ptc.Type='Instrument' AND pt.ParameterTypeID IS NULL"
    );

} elseif ($printToSQL) {
    // generate SQL statements
    $output = "";
    // Fields first since they might generate orphan category entries.
    // delete the parameter_type and parameter_type_category_rel entries
    foreach ($dupFields as $pti => $fieldData) {
        $output .= "DELETE FROM parameter_type_category_rel WHERE ParameterTypeID=$pti;\n";
        $output .= "DELETE FROM parameter_type WHERE ParameterTypeID=$pti;\n";
    }
    // Empty Fields next since they might generate orphan category entries.
    // delete the parameter_type and parameter_type_category_rel entries
    foreach ($emptyFields as $pti) {
        $output .= "DELETE FROM parameter_type_category_rel WHERE ParameterTypeID=$pti;\n";
        $output .= "DELETE FROM parameter_type WHERE ParameterTypeID=$pti;\n";
    }
    // Single query for orphan categories
    $output .= "DELETE ptc ".
      "FROM parameter_type_category ptc ".
        "LEFT JOIN parameter_type_category_rel ptcr USING(ParameterTypeCategoryID) ".
        "LEFT JOIN parameter_type pt USING(ParameterTypeID) ".
      "WHERE ptc.Type='Instrument' AND pt.ParameterTypeID IS NULL;\n";

    //write to file
    try {
        (new SplFileObject($filePath, "w"))->fwrite($output);
    } catch (\RuntimeException $e) {
        // Most likely permission denied
        echo $e->getMessage();
    }
}

function showHelp()
{
    echo "*** DATA DICTIONARY CLEANER ***\n\n";

    echo "Example: php data_dictionary_cleaner.php apply\n";
    echo "Example: php data_dictionary_cleaner.php tosql\n";
    echo "Example: php data_dictionary_cleaner.php check\n\n";

    echo "When the 'tosql' option is used, an SQL file is generated and exported to the following path: \n".
        "%loris_root%/SQL/Archive/autogenerated/single_use/data_dictionary_cleaner.sql\n\n";

    echo "When the 'apply' option is used, the generated SQL is run directly on the database.\n\n";

    die();
}