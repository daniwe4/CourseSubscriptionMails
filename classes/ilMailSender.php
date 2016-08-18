<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailSender.php");
require_once("Services/Mail/classes/class.ilFormatMail.php");
require_once("Services/Mail/classes/class.ilMailFormCall.php");
require_once("Services/User/classes/class.ilObjUser.php");

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

		//$sender = new \ilFormatMail($this->usr_id);
		$sender = new \ilFormatMail(6); //root
		$sender->setSaveInSentbox(true);

		$usr = new \ilObjUser($this->usr_id);

		$arr_dummy = array();
		$arr_type = array(0,0,1);



	/** send external mail using class.ilMimeMail.php
	* @param string to
	* @param string cc
	* @param string bcc
	* @param string subject
	* @param string message
	* @param array attachments
	* @param array type (normal and/or system and/or email)
	* @param integer also as email (0,1)
	* @access	public
	* @return	array of saved data
	*/

		$sender->sendMail(
			$usr->getLogin(), //to
			'', //cc
			'',  //bcc
			$this->subject, 
			$this->message, 
			$arr_dummy,  //attachments
			$arr_type, //type
			1 //also as mail
		);
		
	}
} 