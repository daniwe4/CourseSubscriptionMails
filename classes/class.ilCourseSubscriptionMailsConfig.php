<?php

class ilCourseSubscriptionMailsConfig {
	/**
	 * TODO: Remove me. This is bad, but this is required in parseAMDPlaceholders.
	 * There is no reason for parseAMDPlaceholders to be here, though.
	 */
	private $crs_id;
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
	 * returns the deposited sender_id from table settings
	 *
	 * @return string
	 */
	public function getSenderId() {
		return $this->getSettings()['sender_id'];
	}


	/**
	 * get id from $this->settings and instantiate ilUserObj;
	 * set sender_mail and sender_name.
	 *
	 * @access protected
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
	
	/**
	 * Replace or generate a new db entry in table 'settings'
	 * 
	 * @param string $field 
	 * @param string $value 
	 * 
	 * @return array
	 */
	public function saveAMDTuple($a_field, $a_value) {
		assert('is_string($a_field) === true');

		global $ilDB;

		if(isset($a_field) && $a_field != "" && isset($a_value)) {
			$query = "REPLACE INTO settings (module, keyword, value)"
					."VALUES ('xcsm', '" .$a_field ."', '" . $a_value ."')";
			$ilDB->manipulate($query);
		} else {
			global $ilLog;

			$ilLog->write("Plugin.CSM.Error: Write to table settings failed\nGiven VALUES:\n$a_field\$a_value");
		} 
	}

	/**
	 * Get all difined amd fields
	 * 
	 * @global $ilDB
	 * @return array
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
	}
}


