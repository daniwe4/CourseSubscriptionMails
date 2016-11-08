<?php
require_once('./Services/Component/classes/class.ilPluginConfigGUI.php');


/**
 * Class ilCourseSubscriptionMailsConfigGUI
 *
 * @author 	Nils Haagen <nhaagen@concepts-and-training.de>
 *
 * @ilCtrl_isCalledBy ilCourseSubscriptionMailsConfigGUI:ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 */
class ilCourseSubscriptionMailsConfigGUI extends ilPluginConfigGUI {
	
	
	private $cmd; 
	private $tpl; 
	private $settings; 

	private $sender_id = 6; 
	private $sender_mail = ''; 
	private $sender_name = ''; 

	private $amd_names = array();
	/**
	 * Execute command
	 *
	 * @param
	 * @return
	 */
	function executeCommand()
	{
		global $ilCtrl, $tpl;
		$this->cmd = $ilCtrl->getCmd('configure');
		$this->tpl = &$tpl;
		$this->settings = $this->getSettings();

		switch ($this->cmd) {

			case 'userfield_autocomplete':
				$this->searchUserAutoCompletion();
				break;

			case 'cancel':
				$ilCtrl->redirectByClass('ilobjcomponentsettingsgui','listPlugins');
				break;

			case 'save':
				$sender_login = $_POST['sender_login'];
				$amd_send_mail_field = $_POST['amd_send_mail_field'];
				$amd_send_mail_value = (int)$_POST['amd_send_mail_value'];
				$myresult = $this->saveAMDTuple($amd_send_mail_field, $amd_send_mail_value);
				
				$result = $this->saveUserAsSender($sender_login);
				$this->tpl->setMessage($result[0], $result[1]);

			case 'configure':
			default:
				$this->init_gui();
				$this->readUserValues();
				$this->readAMDNames();
				$this->render_form();
		}

		//$this->performCommand($ilCtrl->getCmd("configure"));
	}



	public function performCommand($cmd) {
		//pass
	}


	/**
	 * set parameters for ilobjcomponentsettingsgui,
	 * set tabs
	 *
	 * @param 
	 * @return 
	 * 
	 */
	protected function init_gui() {
		global $ilCtrl, $ilTabs, $lng;

		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "ctype", $_GET["ctype"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "cname", $_GET["cname"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "slot_id", $_GET["slot_id"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "plugin_id", $_GET["plugin_id"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "pname", $_GET["pname"]);

		$ilTabs->clearTargets();
		$ilTabs->setBackTarget(
			$lng->txt("cmps_plugins"),
			$ilCtrl->getLinkTargetByClass("ilobjcomponentsettingsgui", "listPlugins")
		);

		$this->tpl->setTitle($lng->txt("cmps_plugin").": ".$_GET["pname"]);
		$this->tpl->setDescription($this->cmd);

	}


	/**
	 * render the ui-form ($this->tpl->setContent)
	 *
	 * @param 
	 * @return object ilPropertyFormGUI
	 * 
	 */
	protected function render_form() {
		$form = $this->getForm();
		$this->tpl->setContent($form->getHTML());
	}


	/**
	 * build the ui-form
	 *
	 * @param 
	 * @return object ilPropertyFormGUI
	 * 
	 */
	protected function getForm() {
		global $ilCtrl, $lng;
		require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
		require_once('./Services/Form/classes/class.ilFormSectionHeaderGUI.php');

		$form = new ilPropertyFormGUI();
		$form->setTitle('Configure '. $this->tpl->title);
		$form->setFormAction($ilCtrl->getFormAction($this, "save"));

		// Sender Field
		$sh_sender = new ilFormSectionHeaderGUI();
		$sh_sender->setTitle("Absender");
		
		$label = 'Absender:';
		$user_display =  new ilNonEditableValueGUI($label, "user_display");
		$user_display->setValue($this->sender_name);

		$label = 'Absender ID:';
		$user_display_id =  new ilNonEditableValueGUI($label, "user_display_id");
		$user_display_id->setValue((string)$this->sender_id);		

		$label = 'Absender eMail:';
		$user_display_mail =  new ilNonEditableValueGUI($label, "user_display_mail");
		$user_display_mail->setValue($this->sender_mail);

		$label = 'Benutzer suchen:';
		$user_field = new ilTextInputGUI($label, "sender_login");
		$user_field->setDataSource($ilCtrl->getLinkTarget($this, "userfield_autocomplete", "", true));
		$user_field->setSize(20);
		$user_field->setSubmitFormOnEnter(false);
		$user_field->setParent($form);
		$user_field->readFromSession();
		
		// AMD Field
		$sh_amd = new ilFormSectionHeaderGUI();
		$sh_amd->setTitle("AMD Mails versenden?");

		$label = 'Name des AMD-Felds ob Mails gesendet werden';
		$user_send_mail_field = new ilSelectInputGUI($label, "amd_send_mail_field");
		$user_send_mail_field->setOptions($this->amd_names);
		$user_send_mail_field->setValue($this->settings['amd_field']);
		$user_send_mail_field->setParent($form);

		$label = "Wert für \"Mails senden\":";
		$user_send_mail_value = new ilTextInputGUI($label, "amd_send_mail_value");
		$user_send_mail_value->setValue($this->settings['amd_field_value']);
		$user_send_mail_value->setParent($form);

		$form->addItem($sh_sender);
		$form->addItem($user_display);
		$form->addItem($user_display_id);
		$form->addItem($user_display_mail);
		$form->addItem($user_field);
		
		$form->addItem($sh_amd);
		$form->addItem($user_send_mail_field);
		$form->addItem($user_send_mail_value);

		$form->addCommandButton("save", $lng->txt("save"));
		$form->addCommandButton("cancel", $lng->txt("cancel"));

		return $form;
	
	}

	/**
	 * Show auto complete results
	 */
	protected function searchUserAutoCompletion()
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
	 * build new ilSetting with module "xcsm"
	 *
	 * @access private
	 * @param 
	 *
	 * @return object ilSetting
	 * 
	 */

	private function getSettings() {

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
	 * lookup user by login, write to DB if found.
	 *
	 * @access private
	 * @param string $a_login
	 *
	 * @return array (string status, string text)
	 * 
	 */
	private function saveUserAsSender($a_login) {
		require_once './Services/User/classes/class.ilObjUser.php';

		$user_id = ilObjUser::getUserIdByLogin($a_login);
		if($user_id) {

			//$this->settings->set('sender_id', $user_id);
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


	/**
	 * get id from $this->settings and instantiate ilUserObj;
	 * set sender_mail and sender_name.
	 *
	 * @access private
	 * @param 
	 * @return 
	 * 
	 */
	private function readUserValues() {
	
		$this->sender_id = $this->settings['sender_id'];

		require_once './Services/User/classes/class.ilObjUser.php';
		$user = new ilObjUser($this->sender_id);
			
		$this->sender_mail = $user->getEmail();
		$this->sender_name = $user->getFullname();
	}
	
	private function saveAMDTuple($field, $value) {
		assert(is_string($field) === true);
		assert(is_int($value) === true);
		
		global $ilDB;
		if(isset($field) && $field != "" && isset($value)) {
			$query = "REPLACE INTO settings (module, keyword, value)"
					."VALUES ('xcsm', 'amd_field', $field)";
			$ilDB->manipulate($query);
			$query = "REPLACE INTO settings (module, keyword, value)"
					."VALUES ('xcsm', 'amd_field_value', $value)";
			$ilDB->manipulate($query);
			$this->settings['amd_field'] = $field;
			$this->settings['amd_field_value'] = $value;
		}
		
	}
	
	/**
	 * 
	 * @global type $ilDB
	 */
	private function readAMDNames() {
		global $ilDB;
		
		$amd_names = array();
		$query = "SELECT field_id, title FROM adv_mdf_definition WHERE field_type = 5";
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
}

?>
