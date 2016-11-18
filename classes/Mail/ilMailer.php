<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

require_once(__DIR__ . "/Mailer.php");

/**
 * @inherit
 */
class ilMailer implements Mailer {
	public function __construct($a_usr) {
		$this->usr = $a_usr;
	}

	public function getFullname() {
		return $this->usr->getFullname();
	}
	
	public function getEmailAddress() {
		return $this->usr->getEmail();
	}
}