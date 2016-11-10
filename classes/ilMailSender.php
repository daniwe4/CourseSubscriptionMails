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
		$usr_id = $a_template->getUserId();
		$message = $a_template->getMessage();
		$subject = $a_template->getSubject();
		
		$csm_conf = new \ilCourseSubscriptionMailsConfig();
		$mail = new \ilMail($csm_conf->getSenderId());
		$sender = new \PHPMailer();
		$usr = new \ilObjUser($usr_id);
		
		$iCal = new CourseSubscriptionMailsICalGenerator($this->crs_id, $this->usr_id, $a_template);
		$attach_file = $settings->sendAttachment($iCal);

		$sender->AltBody = $sender->html2text($message); // Plain Text
		$sender->Body = $message; // HTML Text
		$sender->Subject = $subject;
		if(is_array($attach_file) && !empty($attach_file)) {
			$sender->addAttachment($attach_file[0]);
		}
		$sender->addAddress($usr->getLogin());
		$sender->addCC($usr->getEmail());
		$mail->saveInSentbox($attach_file[0], $usr->getEmail(), "", "", "custom", $subject, $message);
		$sender->send();
	}
}
