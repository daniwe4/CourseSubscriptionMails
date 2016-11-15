<?php
namespace CaT\Plugins\CourseSubscriptionMails\Plugin;

require_once('./Services/Component/classes/class.ilPluginConfigGUI.php');
require_once(__DIR__ . '/PluginConfig.php');

/**
 * Class PluginConfigGUI
 *
 * @author 	Nils Haagen <nhaagen@concepts-and-training.de>
 *
 * @ilCtrl_isCalledBy PluginConfigGUI:ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 */
class PluginConfigGUI extends ilPluginConfigGUI {
	
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
		$this->cfg = new \PluginConfig();
		
		switch ($this->cmd) {

			case 'userfield_autocomplete':
				$this->cfg->searchUserAutoCompletion();
				break;

			case 'cancel':
				$ilCtrl->redirectByClass('ilobjcomponentsettingsgui','listPlugins');
				break;

			case 'save':
				$sender_login = $_POST['sender_login'];
				$result = $this->cfg->saveUserAsSender($sender_login);
				$this->tpl->setMessage($result[0], $result[1]);
				
				$amd_send_mail_field = $_POST['amd_send_mail_field'];
				$amd_send_mail_value = $_POST['amd_send_mail_value'];
				$amd_result = $this->cfg->saveAMDTuple($amd_send_mail_field, $amd_send_mail_value);
				$this->tpl->setMessage($amd_result[0], $amd_result[1]);
			case 'configure':
			default:
				$this->init_gui();
				$this->cfg->readUserValues();
				$this->cfg->readAMDNames();
				$this->render_form();
		}

		//$this->performCommand($ilCtrl->getCmd("configure"));
	}



	public function performCommand($cmd) {
		//pass
	}


	/**
	 * set parameters for ilobjcomponentsettingsgui,
	 * set tabss
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
		$user_display->setValue($this->cfg->getSenderName());

		$label = 'Absender ID:';
		$user_display_id =  new ilNonEditableValueGUI($label, "user_display_id");
		$user_display_id->setValue((string)$this->cfg->getSenderId());		

		$label = 'Absender eMail:';
		$user_display_mail =  new ilNonEditableValueGUI($label, "user_display_mail");
		$user_display_mail->setValue($this->cfg->getSendermail());

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
		$user_send_mail_field->setOptions($this->cfg->getAMDNames());
		$user_send_mail_field->setValue($this->cfg->getSettings()['amd_field']);
		$user_send_mail_field->setParent($form);

		$label = "Wert für \"Mails senden\":";
		$user_send_mail_value = new ilTextInputGUI($label, "amd_send_mail_value");
		$user_send_mail_value->setValue($this->cfg->getSettings()['amd_field_value']);
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
}
