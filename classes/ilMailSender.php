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
	//protected $template;


	/**
	 * @inerhitdoc
	 */
	public function sendMail($a_template) {
		
		$usr_id = $a_template->getUserId();
		//$crs_id = $a_template->getCrsId();
		$message = $a_template->getMessage();
		$subject = $a_template->getSubject();
		$attachments = array();
		$attachments = $a_template->getAttachments();

		$arr_type = array(0,0,1);

		
		//sender is fix:
		//$sender = new \ilFormatMail(6); //root
		$sender = new \ilFormatMail(3566); //support-user
		
		$sender->setSaveInSentbox(true);

		//recipient:
		$usr = new \ilObjUser($usr_id);


		/** send external mail using class.ilMimeMail.php
		* @param string to
		* @param string cc
		* @param string bcc
		* @param string subject
		* @param string message
		* @param array attachments
		* @param array type (normal and/or system and/or email)
		* @param integer also as email (0,1)
		*
		* @access	public
		* @return	array of saved data
		*/
		print_r($sender->sendMail(
			$usr->getLogin(), //to
			'', //cc
			'',  //bcc
			$subject, 
			$message, 
			$attachments, 
			$arr_type, //type
			1 //also as mail
		));
		
	}
} 