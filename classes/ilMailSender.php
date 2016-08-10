<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailSender.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;

class ilMailSender implements Mails\interfaces\MailSender {
	public function sendMail($userID, $subject, $message) {
		
	}
}