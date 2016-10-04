<?php
/**
 * This file handles the Family History for LORIS
 *
 * PHP Version 5
 *
 * @category LORIS
 * @package  Main
 * @author   Justin Kat <justinkat@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris
 */
/**
 * Family History Class
 *
 * This class is for the Family History
 *
 * @category LORIS
 * @package  Main
 * @author   Justin Kat <justinkat@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris
*/

require_once "NDB_Form.class.inc";
require_once "Utility.class.inc";

class NDB_Form_family_history extends NDB_Form
{

    var $indent = "&nbsp;&nbsp;&nbsp;&nbsp;";

    /**
     * Determine if the user should have access to this page.
     * Currently tied to data_entry permission.
     *
     * @return boolean True if the user should have access
     */
    function _hasAccess()
    {
        // create user object
        $user =& User::singleton();
        if (Utility::isErrorX($user)) {
            return PEAR::raiseError("User Error: ".$user->getMessage());
        }
        // check that the $candID is passed b/c the profile needs to be opened
        if ($this->identifier != $_REQUEST['candID']) {
            return PEAR::raiseError(
                "NDB_Form_family_history::_access: "
                . "Candidate Profile Error ($this->identifier)"
            );
        }
        // check user permissions
        return ($user->hasPermission('data_entry'));
    }

    /**
     * Process the submitted values, called after validation.
     *
     * @param array $values The values submitted by the user
     *                      to the form.
     *
     * @return boolean True on success
     */
    function _process($values)
    {
        if (!is_array($values) || count($values) === 0) {
            return true;
        }
        $DB =& Database::singleton();

        unset($values['action']);

        if ($_POST['action'] == 'firstdegree') {
            $success = $DB->insert("family_history_first_degree", $values);
        } elseif ($_POST['action'] == 'adother') {
            $success = $DB->insert("family_history_ad_other", $values);
        } elseif ($_POST['action'] == 'memoryproblemothers') {
            $success = $DB->insert("family_history_memory_problem_other", $values);
        } elseif ($_POST['action'] == 'delete') {
            if ($values['table'] === 'family_history_first_degree' || $values['table'] === 'family_history_ad_other' || $values['table'] === 'family_history_memory_problem_other') {
                $success = $DB->delete($values['table'], array('CandID'=>$this->identifier,'ID'=>$values['entry_ID']));
            }
        }

        $this->tpl_data['success'] = true;

        $this->form->freeze();
    }

    /**
     * Constructor called by loris to setup the tpl_data
     * array
     *
     * @return null
     */
    function family_history()
    {

        $this->tpl_data['CandID'] = $this->identifier;
        $config =& NDB_Config::singleton();
        $DB =& Database::singleton();

        $this->tpl_data['family_history_first_degree_details'] = $DB->pselect(
            "SELECT * FROM family_history_first_degree WHERE CandID = :cid",
            array('cid'=>$this->identifier)
        );

        $this->tpl_data['family_history_ad_other_details'] = $DB->pselect(
            "SELECT * FROM family_history_ad_other WHERE CandID = :cid",
            array('cid'=>$this->identifier)
        );

        $this->tpl_data['family_history_memory_problem_other_details'] = $DB->pselect(
            "SELECT * FROM family_history_memory_problem_other WHERE CandID = :cid",
            array('cid'=>$this->identifier)
        );

        $family_members = array('grandmother'=>'Grandmother','grandfather'=>'Grandfather','mother'=>'Mother','father'=>'Father','brother'=>'Brother', 'sister'=>'Sister','son'=>'Son','daughter'=>'Daughter','aunt/uncle'=>'Aunt/Uncle','niece/nephew'=>'Niece/Nephew','half-sibling'=>'Half-sibling','first-cousin'=>'First-cousin','other'=>'Other');
        $ages = array('20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29','30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38','39'=>'39','40'=>'40','41'=>'41','42'=>'42','43'=>'43','44'=>'44','45'=>'45','46'=>'46','47'=>'47','48'=>'48','49'=>'49','50'=>'50','51'=>'51','52'=>'52','53'=>'53','54'=>'54','55'=>'55','56'=>'56','57'=>'57','58'=>'58','59'=>'59','60'=>'60','61'=>'61','62'=>'62','63'=>'63','64'=>'64','65'=>'65','66'=>'66','67'=>'67','68'=>'68','69'=>'69','70'=>'70','71'=>'71','72'=>'72','73'=>'73','74'=>'74','75'=>'75','76'=>'76','77'=>'77','78'=>'78','79'=>'79','80'=>'80','81'=>'81','82'=>'82','83'=>'83','84'=>'84','85'=>'85','86'=>'86','87'=>'87','88'=>'88','89'=>'89','90'=>'90','91'=>'91','92'=>'92','93'=>'93','94'=>'94','95'=>'95','96'=>'96','97'=>'97','98'=>'98','99'=>'99','100'=>'100','101'=>'101','102'=>'102','103'=>'103','104'=>'104','105'=>'105','106'=>'106','107'=>'107','108'=>'108','109'=>'109','110'=>'110','111'=>'111','112'=>'112','113'=>'113','114'=>'114','115'=>'115','116'=>'116','117'=>'117','118'=>'118','119'=>'119','120'=>'120','not_applicable'=>'Not Applicable','not_answered'=>'Not Answered');
        $not_answered = array('not_answered'=>'Not Answered');
        $yes_no = array('Y'=>'Yes','N'=>'No','not_answered'=>'Not Answered');
        $parental_sides = array('maternal'=>'Maternal','paternal'=>'Paternal','not_applicable'=>'Not Applicable','not_answered'=>'Not Answered');
        $tables = array('family_history_first_degree'=>'First Degree','family_history_ad_other'=>'AD Other','family_history_memory_problem_other'=>'Memory Problem Other');

        $this->tpl_data['family_members'] = $family_members;
        $this->tpl_data['living_ages'] = $ages;
        $this->tpl_data['death_ages'] = $ages;
        $this->tpl_data['death_cause_statuss'] = $not_answered;
        $this->tpl_data['ad_dementias'] = $yes_no;
        $this->tpl_data['ad_dementia_ages'] = $ages;
        $this->tpl_data['diagnosis_history_statuss'] = $not_answered;
        $this->tpl_data['parental_sides'] = $parental_sides;
        $this->tpl_data['other_memory_problems_statuss'] = $not_answered;
        $this->tpl_data['tables'] = $tables;

    }

}
?>
