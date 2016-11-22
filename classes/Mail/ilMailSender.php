<?php

namespace CaT\Plugins\CourseSubscriptionMails\Mail;

require_once(__DIR__ . "/MailSender.php");
require_once(__DIR__ . "/../class.ilCourseSubscriptionMailsConfig.php");
require_once(__DIR__ . "/../../../../../../../../../Services/Mail/classes/class.ilMail.php");
require_once(__DIR__ . "/../../../../../../../../../Services/Mail/phpmailer/class.phpmailer.php");
require_once(__DIR__ . "/../../../../../../../../../Services/User/classes/class.ilObjUser.php");


/**
 * Provides a connection to the ILIAS mail system
 */
class ilMailSender implements MailSender {

	/**
	 * @inheritdoc
	 */
	public function sendMail(ICalGenerator $a_iCal, MailTemplate $a_template, Mailer $a_recipient, Mailer $mail_from) {
		global $ilLog;

		$php_mailer 		= $this->buildPHPMail();
		$mail				= $this->buildMail($a_template);
		$plain_text			= $php_mailer->html2text(html_entity_decode($a_template->getMailPieces("BODY")));
		$html_text			= $a_template->getMailPieces("BODY");
		$subject			= $php_mailer->html2text(html_entity_decode($a_template->getMailPieces("SUBJECT")));
		$sender_mail 		= $mail_from->getEmailAddress();
		$sender_fullname 	= $mail_from->getFullname();
		$recipient_mail		= $a_recipient->getEmailAddress();
		$recipient_fullname	= $a_recipient->getFullname();

		// Generate an ICal attachment
		$attach_file = $a_iCal->buildICal();
		if(is_array($attach_file) && !empty($attach_file)) {
			$php_mailer->addAttachment($attach_file[0]);
		}

		// Setup mail
		$php_mailer->setFrom($sender_mail);
		$php_mailer->CharSet = "UTF-8";
		$php_mailer->AltBody =  $plain_text;
		$php_mailer->Body =  $html_text;
		$php_mailer->Subject = $subject;
		$php_mailer->addAddress($recipient_mail);
		if($php_mailer->send()) {
			$ilLog->write("Plugin.CSM.Success: Send Mail to " .$recipient_mail);
		} else {
			$ilLog->write("Plugin.CSM.Error: Mail couldn´t be sent to " .$recipient_mail);
		}
		
		// Setup mail to push in sentbox from sender
		$this->saveInSendBox($a_template, $attach_file, $sender_mail, $subject, $plain_text);
	}


	protected function saveInSendBox($a_template, $a_attach_file, $a_sender_mail, $a_subject, $a_plain_text) {
		global $ilLog;
		$mail = $this->buildMail($a_template);
		if($mail->saveInSentbox($a_attach_file, $a_sender_mail, "", "", "custom", $a_subject, $a_plain_text)) {
			$ilLog->write("Plugin.CSM.Success: Save Mail in Sendbox from " .$a_sender_fullname);
			return true;
		} else {
			$ilLog->write("Plugin.CSM.Error: Mail couldn´t be save into Sendbox from " .$a_sender_fullname);
			return false;
		}
	}

	protected function buildMail($a_template) {
		return new \ilMail($a_template->getSenderId());
	}

	protected function buildPHPMail() {
		return new \PHPMailer();
	}
}
