<?php

namespace CaT\Plugins\CourseSubscriptionMails\business;

use CaT\Plugins\CourseSubscriptionMails as Mails;


/**
 * Slelects the correct mail format and send the email
 */
class SendCorrectMailToUser {
	public $mail_template;
	protected $mail_sender;

	public function __construct(Mails\interfaces\MailTemplate $mail_template, Mails\interfaces\MailSender $mail_sender) {
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
	public function sendCorrectMail($event_name, $usr_id, $crs_id) {
		assert(is_string($event_name));
		assert(is_int($usr_id) && $usr_id >= 0);
		assert(is_int($crs_id) && $crs_id > 0);

		$message = $this->mail_template->getMailFor($event_name, $usr_id, $crs_id);

		if($message !== "") {
			$this->mail_sender->sendMail($usr_id, 'testmail', $message);
		}
	}
}