<?php
namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/class.ilCourseSubscriptionMailsConfig.php");

/**
 * Defines the settings for mail delivery depending on events
 */
class CourseSubscriptionMailsSettings {

	protected $possible_events = array(
		//"addSubscriber", 
		"addParticipant",
		"addToWaitingList",
		"deleteParticipant",
		"removeFromWaitingList", 
		"remindDueCourse"
	//	"moveUpOnWaitingList"
	);

	public function __construct($a_event) {
		$this->event_name = $a_event;
		$this->cfg = new \ilCourseSubscriptionMailsConfig();
	}
	
	/**
	 * Returns true if the Event is handled by the plugin 
	 * otherwise false
	 * 
	 * @param 	string 	$event_name
	 *
	 * @return 	boolean
	 */
	public function isPluginEvent() {

		if(in_array($this->event_name, $this->possible_events)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function sendAttachment(CourseSubscriptionMailsICalGenerator $iCalGen) {
		if($this->event_name === "addParticipant") {
			return $iCalGen->buildICal();
		} else {
			return null;
		}
	}
	/**
	 * 
	 * @global type $tpl
	 */
	public function getMailHtml($a_usr, $a_crs, $a_event) {
		$skin_path = "./Customizing/global/skin/MailTemplates/";
		
		$mytpl = new \ilTemplate($skin_path . "tpl.csm_" . $a_event .".html", TRUE, TRUE);
		$mytpl->setCurrentBlock("TEXT");
		$placeholders = $mytpl->getBlockvariables("TEXT");
		$arr = $this->cfg->parsePlaceholders($placeholders, $a_usr, $a_crs);
		foreach($arr as $key => $value) {
			$mytpl->setVariable($key, $value);
		}
		$mytpl->parseCurrentBlock();
		$html = $mytpl->get();
		
		return $html;
		
	}
	
	public function isCSMEnabled($crs_id) {
		global $ilDB;
		$enabled = false;
		
		$query = "SELECT keyword, value "
			   . "FROM settings "
			   . "WHERE module "
			   . "LIKE 'xcsm' AND (keyword LIKE 'amd_field' OR keyword LIKE 'amd_field_value')"; 
		
		$result = $ilDB->query($query);
		
		while($row = $ilDB->fetchAssoc($result)) {
			$plugin_settings[$row['keyword']] = $row['value'];
		}
		
		$query = "SELECT value FROM adv_md_values_int WHERE field_id = " . $plugin_settings['amd_field'] . " AND obj_id = '" . $crs_id . "';";
		$result  = $ilDB->query($query);
		$value = $ilDB->fetchAssoc($result)['value'];
		
		
		
		if(isset($value) && $plugin_settings['amd_field_value'] === $value) {
			$enabled = true;
		}
		return $enabled;
	}
}
