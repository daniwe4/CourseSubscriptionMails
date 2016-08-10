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
	 * @param 	int 		$userID
	 * @param 	string 		$subject
	 * @param 	string 		$message 
	 *
	 * @return null 
	 */
	public function sendMail($userID, $subject, $message);

}