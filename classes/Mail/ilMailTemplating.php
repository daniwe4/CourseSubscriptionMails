<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

require_once(__DIR__ . "/MailTemplate.php");
require_once(__DIR__ . "/../../../../../../../../../Modules/Course/classes/class.ilObjCourse.php");



class ilMailTemplating implements MailTemplate {
	/**
	 * @var string
	 */
	private $event_name;

	/**
	 * @var object
	 */
	private $usr;

	/**
	 * @var object
	 */
	private $crs;

	/**
	 * @var int
	 */
	private $sender_id;

	/**
	 * @var int
	 */
	private $usr_id;
	
	/**
	 * @var int
	 */
	private $crs_id;

	
	/**
	 * Init ilMailTemplating
	 * 
	 * @param string $a_event_name 
	 * @param int $a_usr_id 
	 * @param int $a_crs_id 
	 * @return null
	 */
	public function __construct($a_event_name, $a_usr_id, $a_crs_id) {
		assert('is_string($a_event_name) === true');
		assert('is_int($a_usr_id) === true');
		assert('is_int($a_crs_id) === true');

		$this->setEventName($a_event_name);
		$this->setUser(new \ilObjUser($a_usr_id));
		$this->setCourse(new \ilObjCourse($a_crs_id, false));
		$this->usr_id = $a_usr_id;
		$this->crs_id = $a_crs_id;
	}

	/**
	 * set event-name
	 *
	 * @param  string $a_event_name
	 * @return null
	 *
	 */
	public function setEventName($a_event_name) {
		assert('is_string($a_event_name)');

		$this->event_name = $a_event_name;
	}

	/**
	 * get current event-name 
	 *
	 * @return 	string 	
	 *
	 */
	public function getEventName() {
		return $this->event_name;
	}

	/**
	 * set user-id 
	 *
	 * @param 	int 	$a_usr_id
	 * @return 	null 
	 *
	 */
	public function setUser($a_usr) {
		assert('is_object($a_usr) ===true');

		$this->usr = $a_usr;
	}

	/**
	 * get current user-id
	 *
	 * @return 	int 	
	 *
	 */
	public function getUser() {
		return $this->usr;
	}

	/**
	 * set course-id
	 *
	 * @param 	int 	$a_crs_id
	 * @return 	null 
	 *
	 */
	public function setCourse($a_crs) {
		assert('is_object($a_crs) === true');

		$this->crs = $a_crs;
	}

	/**
	 * get current course-id 
	 *
	 * @return 	int 	
	 *
	 */
	public function getCourse() {
		return $this->crs;
	}

	/**
	 * get current user id
	 * 
	 * @return int
	 */
	public function getUsrId() {
		return $this->usr_id;
	}

	/**
		 * get current course id
		 * 
		 * @return int
		 */	
	public function getCrsId() {
		return $this->crs_id();
	}
	
	/**
	 * set sender id
	 *
	 * @param 	int 	$a_id
	 * @return
	 *
	 */
	private function setSenderId($a_id) {
		assert('is_int($a_id) === true');
		assert('$a_id > 0');

		$this->sender_id = $a_id;
	}

	/**
	 * get sender id
	 *
	 * @return 	int
	 *
	 */
	public function getSenderId() {
		return $this->getUserValuesFromDb()['sender_id'];
	}

	/**
	 * get the user values from settings table
	 *
	 * @return 	array user values
	 */
	private function getUserValuesFromDb() {
		global $ilDB;

		$setting = array();
		$query = "SELECT * FROM settings WHERE module='xcsm'";
		$res = $ilDB->query($query);

		while ($row = $ilDB->fetchAssoc($res)) {
			$setting[$row["keyword"]] = $row["value"];
		}
		
		if(! $setting['sender_id']) {
			$setting['sender_id'] = 6;
		}

		return $setting;
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
		$possible_events = array(
		//"addSubscriber", 
		"addParticipant",
		"addToWaitingList",
		"deleteParticipant",
		"removeFromWaitingList", 
		"remindDueCourse"
		//	"moveUpOnWaitingList"
		);

		if(in_array($this->event_name, $possible_events)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks, wether the CSM-plugin is enabled
	 * 
	 * @return type boolean $enabled true/enabled false/disabled
	 */
	public function isCSMEnabled() {

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
		
		$a_crs_id = $this->getCourse()->getId();
		$query = "SELECT value "
				."FROM adv_md_values_text "
				."WHERE field_id = " . $plugin_settings['amd_field'] . " AND obj_id = '" . $this->getCourse()->getId() . "';";

		$result  = $ilDB->query($query);
		$value = $ilDB->fetchAssoc($result)['value'];
		
		
		
		if(isset($value) && $plugin_settings['amd_field_value'] === $value) {
			$enabled = true;
		}
		return $enabled;
	}

	/**
	 * Returns a user, course and event specifig piece
	 * from a template file
	 * 
	 * @param string $a_block parese only this block in file 
	 * 
	 * @return type string
	 */
	public function getMailHtml($a_block) {
		assert('is_string($a_block) === true');
		
		$mail_tpl = new \ilTemplate("tpl.csm_" . $this->getEventName() .".html", TRUE, TRUE, "Customizing/global/plugins/Services/EventHandling/EventHook/CourseSubscriptionMails");
		$mail_tpl->setCurrentBlock($a_block);
		$placeholders = $mail_tpl->getBlockvariables($a_block);
		
		$arr = $this->parsePlaceholders($placeholders, $this->getUser(), $this->getCourse());

		foreach($arr as $key => $value) {
			$mail_tpl->setVariable($key, htmlentities($value));
		}

		// fixes a problem: without any placeholder in a block
		// 					we get nothing back
		$mail_tpl->setVariable("DO_NOT_DELETE", "");
		$mail_tpl->parseCurrentBlock();
		
		return $mail_tpl->get();
	}

	/**
	 * @inheritdoc
	 */
	public function getMailPieces($a_which) {
		assert('is_string($a_which)');
		assert('$a_which === "BODY" || $a_which == "SUBJECT"');

		if($this->isPluginEvent()) {
			return $this->getMailHtml($a_which);
		}
		throw \InvalidArgumentException();
	}

	/**
	 * Delegates parsePlaceholder() calls, and merged all results
	 * 
	 * @param array $placeholders 
	 * 
	 * @return array a merge of all method calls
	 */
	public function parsePlaceholders(array $placeholders) {
		assert(is_array($placeholders) === true);

		$all_placeholders = array();
		$all_placeholders[] = $this->parseAMDPlaceholders($placeholders);
		$all_placeholders[] = $this->parseUserPlaceholders($placeholders);
		$all_placeholders[] = $this->parseCoursePlaceholders($placeholders);
		$all_placeholders[] = $this->parseInstallationPlaceholders($placeholders);
		
		return call_user_func_array("array_merge", $all_placeholders);
	}

	private function parseAMDPlaceholders(array $placeholders) {
		assert(is_array($placeholders) === true);

		global $ilDB;
		$matches = array();
		$res_array = array();

		$query = "SELECT * FROM adv_mdf_definition";

		$result = $ilDB->query($query);
		while($row = $ilDB->fetchAssoc($result)) {
			if(in_array($row['title'], $placeholders)) {
				$matches[$row['title']] = ["title" => $row['title'],
										   "field_id" => $row['field_id'],
										   "field_type" => $row['field_type']];
			}
		}

		foreach($matches as $match) {
			switch ($match['field_type']) {
				case 1:
				case 2:
					$table_name = "text";
					break;
				case 3:
					$table_name = "date";
					break;
				case 4:
					$table_name = "datetime";
				case 5:
					$table_name = "int";
				default:
					break;
			}

			$query = "SELECT * "
					."FROM adv_md_values_" .$table_name 
					." WHERE field_id = " . $match['field_id'] . " AND obj_id = " . $ilDB->quote($this->getCourse()->getId(), "integer");

			$result = $ilDB->query($query);
			while($row = $ilDB->fetchAssoc($result)) {
				$res_array[$match['title']] = $row['value'];
			}
		}
		return $res_array;
	}

	/**
	 * Replaces all placeholders with userdata
	 * 
	 * @param array $placeholders
	 * 
	 * @return array
	 */
	private function parseUserPlaceholders(array $placeholders) {

		$a_usr = $this->getUser();
		$ret_arr = array();
		
		foreach($placeholders as $placeholder) {
			if($placeholder === "MAIL_SALUTATION") {
				if($a_usr->getGender() === "w") {
					$ret_arr["MAIL_SALUTATION"] = "Sehr geehrte Frau";
				} else {
					$ret_arr["MAIL_SALUTATION"] = "Sehr geehrter Herr";
				}
			}
			
			if($placeholder === "FIRST_NAME") {
				$ret_arr["FIRST_NAME"] = $a_usr->getFirstname();
			}
			
			if($placeholder === "LAST_NAME") {
				$ret_arr["LAST_NAME"] = $a_usr->getLastname();
			}
			
			if($placeholder === "LOGIN") {
				$ret_arr["LOGIN"] = $a_usr->getLogin();
			}
		}
		return $ret_arr;
	}
	
	/**
	 * Replace all placeholders with course data
	 * 
	 * @param array $placeholders
	 * 
	 * @return array
	 */
	private function parseCoursePlaceholders(array $placeholders) {

		$a_crs = $this->getCourse();
		$ret_arr = array();
		
		foreach($placeholders as $placeholder) {
			if($placeholder === "COURSE_TITLE") {
				$ret_arr["COURSE_TITLE"] = $a_crs->getTitle();
			}
			
			if($placeholder === "COURSE_LINK") {
				// TODO: add COURS_LINK
			}
		}
		return $ret_arr;
		
		
	}
	
	/**
	 * Replaces all placeholders while installation
	 * 
	 * @param array $placeholders 
	 * @return array
	 */
	private function parseInstallationPlaceholders(array $placeholders) {
		assert(is_array($placeholders) === true);

		return array();
	}
}
