<?php

class ilCourseSubscriptionMailsConfig {
	/**
	 * TODO: Remove me. This is bad, but this is required in parseAMDPlaceholders.
	 * There is no reason for parseAMDPlaceholders to be here, though.
	 */
	public $crs_id;

	private $settings; 
	private $sender_mail = ''; 
	private $sender_name = ''; 
	
	private $amd_names = array();
	
	public function getSenderMail() {
		return $this->sender_mail;
	}
	
	public function getSenderName() {
		return $this->sender_name;
	}
	
	public function getAMDNames() {
		return $this->amd_names;
	}
	
	public function getSettings() {
				/*
		DO NOT USE ilSetting LIKE THAT.
		IT will horribly reset global $ilSetting!
		$settings = new ilSetting();
		$settings->ilSetting('xcsm'); //also reads.
		return $settings;
		*/
		global $ilDB;
		$setting = array();
		$query = "SELECT * FROM settings WHERE module='xcsm'";
		$res = $ilDB->query($query);
		while ($row = $ilDB->fetchAssoc($res)) {
			$setting[$row["keyword"]] = $row["value"];
		}
		return $setting;
	}
	
	
	
	/**
	 * Show auto complete results
	 */
	public function searchUserAutoCompletion()
	{
		include_once './Services/User/classes/class.ilUserAutoComplete.php';
		$auto = new ilUserAutoComplete();
		$auto->setSearchFields(array('login','firstname','lastname','email'));
		$auto->enableFieldSearchableCheck(false);
		$auto->setMoreLinkAvailable(true);

		if(($_REQUEST['fetchall']))
		{
			$auto->setLimit(ilUserAutoComplete::MAX_ENTRIES);
		}

		echo $auto->getList($_REQUEST['term']);
		exit();
	}

	/**
	 * lookup user by login, write to DB if found.
	 *
	 * @access private
	 * @param string $a_login
	 *
	 * @return array (string status, string text)
	 * 
	 */
	public function saveUserAsSender($a_login) {
		require_once './Services/User/classes/class.ilObjUser.php';

		$user_id = ilObjUser::getUserIdByLogin($a_login);
		if($user_id) {

			global $ilDB;
			$query = "REPLACE INTO settings (module, keyword, value)"
				." VALUES ('xcsm', 'sender_id'"
				." , " .$user_id
				.")";

			$ilDB->manipulate($query);
			$this->settings['sender_id'] = $user_id;
			return array('success', 'user saved.');
		} else {
			return array('failure', 'no such user.');
		}
	}
	
	public function getSenderId() {
		global $ilDB;
		$query = "SELECT value FROM settings WHERE keyword = 'sender_id'";
		$result = $ilDB->query($query);
		$ret = $ilDB->fetchAssoc($result);
		return $ret['value'];
	}


	/**
	 * get id from $this->settings and instantiate ilUserObj;
	 * set sender_mail and sender_name.
	 *
	 * @access private
	 * @param 
	 * @return 
	 * 
	 */
	public function readUserValues() {
	
		$this->sender_id = $this->getSenderId();

		require_once './Services/User/classes/class.ilObjUser.php';
		$user = new ilObjUser($this->sender_id);
			
		$this->sender_mail = $user->getEmail();
		$this->sender_name = $user->getFullname();
	}
	
	public function saveAMDTuple($field, $value) {
		assert(is_string($field) === true);
		assert(is_string($value) === true);

		global $ilDB, $ilLog;
		if(isset($field) && $field != "" && isset($value)) {
			$query = "REPLACE INTO settings (module, keyword, value)"
					."VALUES ('xcsm', 'amd_field', $field)";
			$ilDB->manipulate($query);
			$query = "REPLACE INTO settings (module, keyword, value)"
					."VALUES ('xcsm', 'amd_field_value', '$value')";
			$ilDB->manipulate($query);
			return array('success', 'AMD Field saved.');
		} else {
			return array('failure', 'save error.');
		}
	}

	/**
	 * 
	 * @global type $ilDB
	 */
	public function readAMDNames() {
		global $ilDB;

		$amd_names = array();
		$query = "SELECT field_id, title FROM adv_mdf_definition WHERE field_type = 1 OR field_type = 2";
		$res = $ilDB->query($query);

		while ($row = $ilDB->fetchAssoc($res)) {
			$amd_names[$row['field_id']] = $row['title'];
		}

		$this->amd_names = $amd_names;

		$query = "SELECT value FROM settings WHERE keyword = 'amd_field'";
		$res = $ilDB->query($query);
		$row = $ilDB->fetchAssoc($res);
		if($row) {
			$this->amd_field = $row['value'];
		}

		$query = "SELECT value FROM settings WHERE keyword = 'amd_field_value'";
		$res = $ilDB->query($query);
		$row = $ilDB->fetchAssoc($res);
		if($row) {
			$this->amd_field_value = $row['value'];
		}
	}

	public function parsePlaceholders(array $placeholders, $usr, $crs) {
		$all_placeholders = array();
		$all_placeholders[] = $this->parseAMDPlaceholders($placeholders);
		$all_placeholders[] = $this->parseUserPlaceholders($placeholders, $usr);
		$all_placeholders[] = $this->parseCoursePlaceholders($placeholders, $crs);
		$all_placeholders[] = $this->parseInstallationPlaceholders($placeholders);
		return call_user_func_array("array_merge", $all_placeholders);
	}

	public function parseAMDPlaceholders(array $placeholders) {
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

			$query = "SELECT * FROM adv_md_values_" .$table_name . " WHERE field_id = " . $match['field_id'] . " AND obj_id = " . $ilDB->quote($this->crs_id, "integer");
			$result = $ilDB->query($query);
			while($row = $ilDB->fetchAssoc($result)) {
				$res_array[$match['title']] = $row['value'];
			}
		}
		return $res_array;
	}


	public function parseUserPlaceholders(array $placeholders, $usr) {
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
	
	public function parseCoursePlaceholders(array $placeholders, $crs) {
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
	
	public function parseInstallationPlaceholders(array $placeholders) {
		return array();
	}
}


