<?php

namespace CaT\Plugins\CourseSubscriptionMails\business;

use CaT\Plugins\CourseSubscriptionMails as Mails;


/**
 * Slelects the correct mail format and send the email
 */
class SendCorrectMailToUser {
	protected $mail_template;
	protected $mail_sender;

	public function __construct(Mails\interfaces\MailTemplate $mail_template, Mails\interfaces \MailSender $mail_sender) {
		$this->mail_template = $mail_template;
		$this->mail_sender = $mail_sender;
	}

	/**
	 * Gets the mail template and sends it to the user.
	 *
	 * @param 	string 	$event_name
	 * @param 	int 	$user_id
	 * @param 	int 	$crs_id
	 *
	 * @return null
	 */
	public function sendCorrectMail($event_name, $user_id, $crs_id) {
		assert(is_string($event_name));
		assert(is_int($user_id));
		assert(is_int($crs_id));
		global $ilLog;

		$massege = $this->mail_template->getMailFor($event_name, $user_id, $crs_id);
		$ilLog->write($messege);
		$this->mail_sender->sendMail($user_id, $subject, $massege);
	}
}