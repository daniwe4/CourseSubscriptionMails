<?php
namespace CaT\Plugins\CourseSubscriptionMails\interfaces;

use CaT\Plugins\CourseSubscriptionMails;

/**
 * Interface that abstracts Email functions
 */
interface MailSender {
	
	/**
	 * Sends a mail to User 
	 * 
	 * @param 	int 		$usr_id
	 * @param 	string 		$subject
	 * @param 	string 		$message 
	 *
	 * @return null 
	 */
	public function sendMail($usr_id, $subject, $message);

}