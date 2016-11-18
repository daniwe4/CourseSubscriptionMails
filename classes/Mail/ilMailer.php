<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

require_once(__DIR__ . "/Mailer.php");

/**
 * wraps an ilObjUser object
 */
class ilMailer implements Mailer {
	public function __construct($a_usr) {
		$this->usr = $a_usr;
	}

	/**
	 * return the fullname from a given user object
	 * 
	 * @return string
	 */
	public function getFullname() {
		return $this->usr->getFullname();
	}
	
	/**
	 * return the email address from a given user object
	 * 
	 * @return string
	 */
	public function getEmailAddress() {
		return $this->usr->getEmail();
	}
}