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
		$attachments = $a_template->getAttachments();

		
//print_r($subject);
//print_r($message);
//print_r($attachments);
//die();

		//sender is fix:
		$sender = new \ilFormatMail(6); //root
		//$sender = new \ilFormatMail(3365); //support-user
		$sender->setSaveInSentbox(true);


		$usr = new \ilObjUser($usr_id);
		
		$arr_attach = array();

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
		print_r($sender->sendMail(
			$usr->getLogin(), //to
			'', //cc
			'',  //bcc
			$subject, 
			$message, 
			$arr_attach,  //attachments
			$arr_type, //type
			1 //also as mail
		));
		
	}
} 