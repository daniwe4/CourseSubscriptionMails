<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

require_once(__DIR__ . "/../../interfaces/MailTemplate.php");
require_once("./Modules/Course/classes/class.ilObjCourse.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;



class ilMailTemplating implements Mails\interfaces\MailTemplate {
	private $event_name;
	private $usr_id;
	private $crs_id;
	private $sender_id;

	
	public function __construct($a_event_name, $a_usr_id, $a_crs_id, $a_sender_id) {
		assert(is_string($a_event_name) === true);
		assert(is_int($a_usr_id) === true);
		assert(is_int($a_crs_id) === true);
		assert(is_int($a_sender_id) === true);

		$this->setEventName($a_event_name);
		$this->setUserId($a_usr_id);
		$this->setCourseId($a_crs_id);
		$this->setSenderId($a_sender_id);
	}

	/**
	 * set event-name
	 *
	 * @param  string $a_event_name
	 * @return null
	 *
	 */
	public function setEventName($a_event_name) {
		assert(is_string($a_event_name));

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
	public function setUserId($a_usr_id) {
		assert(is_int($a_usr_id) && $a_usr_id >= 0);

		$this->usr_id = $a_usr_id;
	}

	/**
	 * get current user-id
	 *
	 * @return 	int 	
	 *
	 */
	public function getUserId() {
		return $this->usr_id;
	}

	/**
	 * set course-id
	 *
	 * @param 	int 	$a_crs_id
	 * @return 	null 
	 *
	 */
	public function setCourseId($a_crs_id) {
		assert(is_int($a_crs_id) && $a_crs_id > 0);

		$this->crs_id = $a_crs_id;
	}

	/**
	 * get current course-id 
	 *
	 * @return 	int 	
	 *
	 */
	public function getCourseId() {
		return $this->crs_id;
	}

	
	/**
	 * set sender id
	 *
	 * @param 	int 	$a_id
	 * @return
	 *
	 */
	private function setSenderId($a_id) {
		assert(is_int($a_id) && $a_id > 0);

		$this->sender_id = $a_id;
	}

	/**
	 * get sender id
	 *
	 * @return 	int
	 *
	 */
	public function getSenderId() {
		return $this->sender_id;
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

	public function sendAttachment(ICalGenerator $iCalGen) {
		// return $iCalGen->buildICal();
		return null;
	}

	/**
	 * Checks, wether the CSM-plugin is enabled
	 * 
	 * @param type int $crs_id 
	 * @return type boolean $enabled true/enabled false/disabled
	 */
	public function isCSMEnabled($crs_id) {
		assert(is_int($crs_id) === true);

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
		
		$query = "SELECT value "
				."FROM adv_md_values_text "
				."WHERE field_id = " . $plugin_settings['amd_field'] . " AND obj_id = '" . $crs_id . "';";

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
	 * @param object $a_usr 
	 * @param object $a_crs 
	 * @param string $a_event select file by envent
	 * @param string $a_block parese only this block in file 
	 * @return type string
	 */
	public function getMailHtml($a_usr, $a_crs, $a_event, $a_block) {
		assert(is_object($a_usr) === true);
		assert(is_object($a_crs) === true);
		assert(is_string($a_event) === true);
		assert(is_string($a_block) === true);

		$skin_path = "./Customizing/global/skin/MailTemplates/";
		
		$mail_tpl = new \ilTemplate($skin_path . "tpl.csm_" . $a_event .".html", TRUE, TRUE);
		$mail_tpl->setCurrentBlock($a_block);
		$placeholders = $mail_tpl->getBlockvariables($a_block);
		
		$arr = $this->parsePlaceholders($placeholders, $a_usr, $a_crs);

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
		assert(is_string($a_which) === true);
		assert($a_which === "BODY" || $a_which == "SUBJECT");
		
		$usr = new \ilObjUser($this->getUserId());
		$crs = new \ilObjCourse($this->getCourseId(), false);

		switch ($this->getEventName()) {
			case 'addParticipant':
				return $this->getMailHtml($usr, $crs, "addParticipant", $a_which);

			case 'addToWaitingList':
				return $this->getMailHtml($usr, $crs, "addToWaitingList", $a_which);

			case 'deleteParticipant':
				return $this->getMailHtml($usr, $crs, "deleteParticipant", $a_which);

			case 'removeFromWaitingList':
				return $this->getMailHtml($usr, $crs, "removeFromWaitingList", $a_which);

			case 'remindDueCourse':
				return $this->getMailHtml($usr, $crs, "remindDueCourse", $a_which);
		}
		throw \InvalidArgumentException();
	}

	/**
	 * Delegates parsePlaceholder() calls, and merged all results
	 * 
	 * @param array $placeholders 
	 * @param object $usr 
	 * @param object $crs 
	 * @return array a merge of all method calls
	 */
	public function parsePlaceholders(array $placeholders, $usr, $crs) {
		assert(is_array($placeholders) === true);
		assert(is_object($usr) === true);
		assert(is_object($crs) === true);

		$all_placeholders = array();
		$all_placeholders[] = $this->parseAMDPlaceholders($placeholders);
		$all_placeholders[] = $this->parseUserPlaceholders($placeholders, $usr);
		$all_placeholders[] = $this->parseCoursePlaceholders($placeholders, $crs);
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
					." WHERE field_id = " . $match['field_id'] . " AND obj_id = " . $ilDB->quote($this->crs_id, "integer");

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
	 * @param object $usr 
	 * @return array
	 */
	private function parseUserPlaceholders(array $placeholders, $usr) {
		asser(is_array($placeholders) === true);
		assert(is_object($usr) === true);

		$ret_arr = array();
		
		foreach($placeholders as $placeholder) {
			if($placeholder === "MAIL_SALUTATION") {
				if($user->getGender === "w") {
					$ret_arr["MAIL_SALUTATION"] = "Sehr geehrte Frau";
				} else {
					$ret_arr["MAIL_SALUTATION"] = "Sehr geehrter Herr";
				}
			}
			
			if($placeholder === "FIRST_NAME") {
				$ret_arr["FIRST_NAME"] = $usr->getFirstname();
			}
			
			if($placeholder === "LAST_NAME") {
				$ret_arr["LAST_NAME"] = $usr->getLastname();
			}
			
			if($placeholder === "LOGIN") {
				$ret_arr["LOGIN"] = $usr->getLogin();
			}
		}
		return $ret_arr;
	}
	
	/**
	 * Replaces all placeholders with coursedata
	 * 
	 * @param array $placeholders 
	 * @param object $crs
	 * @return array
	 */
	private function parseCoursePlaceholders(array $placeholders, $crs) {
		asser(is_array($placeholders) === true);
		assert(is_object($crs) === true);

		$ret_arr = array();
		
		foreach($placeholders as $placeholder) {
			if($placeholder === "COURSE_TITLE") {
				$ret_arr["COURSE_TITLE"] = $crs->getTitle();
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
		asser(is_array($placeholders) === true);

		return array();
	}
}
