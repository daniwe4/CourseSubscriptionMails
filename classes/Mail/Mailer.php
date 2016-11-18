<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

/**
 * provide functions to wrap an ilObjUser object
 */
interface Mailer {

	/**
	 * should return the fullname from a given user object
	 * 
	 * @return string
	 */
	public function getFullname();
	
	/**
	 * should return the email address from a given user object
	 *
	 * @return string
	 */
	public function getEmailAddress();
}