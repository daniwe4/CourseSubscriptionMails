<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailSender.php");
require_once("Services/Mail/phpmailer/class.phpmailer.php");
require_once("Services/Mail/classes/class.ilMailFormCall.php");
require_once("Services/User/classes/class.ilObjUser.php");
require_once(__DIR__ . "/CourseSubscriptionMailsICalGenerator.php");
require_once(__DIR__ . "/CourseSubscriptionMailsSettings.php");
require_once("Services/Mail/classes/class.ilMail.php");
require_once(__DIR__ . "/class.ilCourseSubscriptionMailsConfig.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;


/**
 * Provides a connection to the ILIAS mail system
 */
class ilMailSender implements Mails\interfaces\MailSender {
	//protected $template;
	public function __construct($usr_id, $crs_id) {
		$this->usr_id = $usr_id;
		$this->crs_id = $crs_id;
		
	}

	/**
	 * @inheritdoc
	 */
	public function sendMail($a_template, $settings) {
		global $ilLog;
		
		$usr_id = $a_template->getUserId();
		$message = $a_template->getMessage();
		$subject = html_entity_decode($a_template->getSubject());
		$csm_conf = new \ilCourseSubscriptionMailsConfig();
		$csm_conf->crs_id = $this->crs_id;
		$mail = new \ilMail($csm_conf->getSenderId());
		$sender = new \PHPMailer();
		$usr = new \ilObjUser($usr_id);
		$msender = new \ilObjUser($csm_conf->getSenderId());
		
		$iCal = new CourseSubscriptionMailsICalGenerator($this->crs_id, $this->usr_id, $a_template);
		$attach_file = $settings->sendAttachment($iCal);
		
		$sender->setFrom($msender->getEmail());
		$sender->CharSet = "UTF-8";
		$sender->AltBody = $sender->html2text(html_entity_decode($message)); // Plain Text
		$sender->Body = $message; // HTML Text
		$sender->Subject = $subject;
		if(is_array($attach_file) && !empty($attach_file)) {
			$sender->addAttachment($attach_file[0]);
		} 
		$sender->addAddress($usr->getLogin());
		$sender->addCC($usr->getEmail());
		
		if($mail->saveInSentbox($attach_file[0], $usr->getEmail(), "", "", "custom", $subject, $message)) {
			$ilLog->write("Plugin.CSM.Success: Save Mail in Sendbox from " .$msender->getLogin());
		} else {
			$ilLog->write("Plugin.CSM.Error: Mail couldn´t be save into Sendbox from " .$msender->getLogin());
		}
		
		if($sender->send()) {
			$ilLog->write("Plugin.CSM.Success: Send Mail to " .$usr->getEmail());
		} else {
			$ilLog->write("Plugin.CSM.Error: Mail couldn´t be sent to " .$usr->getEmail());
		}
		
	}
}
