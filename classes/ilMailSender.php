<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailSender.php");
require_once("Services/Mail/phpmailer/class.phpmailer.php");
require_once("Services/Mail/classes/class.ilMailFormCall.php");
require_once("Services/User/classes/class.ilObjUser.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;


/**
 * Provides a connection to the ILIAS mail system
 */
class ilMailSender implements Mails\interfaces\MailSender {
	//protected $template;


	/**
	 * @inheritdoc
	 */
	public function sendMail($a_template) {
		
		$usr_id = $a_template->getUserId();
		$message = $a_template->getMessage();
		$subject = $a_template->getSubject();
		
		$sender = new \PHPMailer();

		//$sender->setSaveInSentbox(true);

		//recipient:
		$usr = new \ilObjUser($usr_id);
		
		$sender->AltBody = $sender->html2text($message); // Plain Text
		$sender->Body = $message; // HTML Text
		$sender->Subject = $subject;
		$sender->addAddress($usr->getLogin());
		$sender->addCC($usr->getEmail());
		$sender->send();
	}
} 

?>
