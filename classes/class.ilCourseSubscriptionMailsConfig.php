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
		assert('is_string($field) === true');
		assert('is_string($value) === true');

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

		/*
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
		*/
	}


}


