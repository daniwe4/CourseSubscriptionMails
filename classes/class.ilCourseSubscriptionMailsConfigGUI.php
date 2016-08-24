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

				$result = $this->saveUserAsSender($sender_login);
				$this->tpl->setMessage($result[0], $result[1]);

			case 'configure':
			default:
				$this->init_gui();
				$this->readUserValues();
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
	

		$form = new ilPropertyFormGUI();
		$form->setTitle('Configure '. $this->tpl->title);
		$form->setFormAction($ilCtrl->getFormAction($this, "save"));


		// User name, login, email filter
		//include_once("./Services/Form/classes/class.ilTextInputGUI.php");
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


		$form->addItem($user_display);
		$form->addItem($user_display_id);
		$form->addItem($user_display_mail);
		$form->addItem($user_field);

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
		$settings = new ilSetting();
		$settings->ilSetting('xcsm'); //also reads.
		return $settings;
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
			$this->settings->set('sender_id', $user_id);
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
	
		$this->sender_id = $this->settings->get('sender_id', 6);
		require_once './Services/User/classes/class.ilObjUser.php';
		$user =  new ilObjUser($this->sender_id);
			
		$this->sender_mail = $user->getEmail();
		$this->sender_name = $user->getFullname();
	}

}
?>
