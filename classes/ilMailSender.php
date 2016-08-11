<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailSender.php");
require_once(__DIR__ . "/../../../../../../../../Services/Mail/classes/class.ilFormatMail.php");
require_once(__DIR__ . "/../../../../../../../../Services/Mail/classes/class.ilMailFormCall.php");
require_once(__DIR__ . "/../../../../../../../../Services/User/classes/class.ilObjUser.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;


/**
 * Provides a connection to the ILIAS mail system
 */
class ilMailSender implements Mails\interfaces\MailSender {
	protected $usr_id;
	protected $subject;
	protected $message;

	

	/**
	 * @inerhitdoc
	 */
	public function sendMail($usr_id, $subject, $message) {
		assert(is_int($usr_id) && $usr_id >= 0);
		assert(is_string($subject));
		assert(is_string($message));

		$this->usr_id = $usr_id;
		$this->subject = $subject;
		$this->message = $message;

		$sender = new \ilFormatMail($this->usr_id);
		$usr = new \ilObjUser($this->usr_id);
		$arr_dummy = array();
		$arr_type = array(0,0,1);

		$sender->sendMail($usr->getLogin(), '', '', $this->subject, $this->message, $arr_dummy, $arr_type,1);
		
	}
} 