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
	 * @param 	object 	$a_template (instance of ilNaiveMailTemplating)
	 * @param	object	$settings (instance of MailSettings)
	 * @return 	null 
	 */
	public function sendMail($a_template);

}