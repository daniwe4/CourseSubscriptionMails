<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;


interface MailTemplate {
	/**
	 * Get the appropriate mail and/or subjectfor the given event, 
	 * where placeholders are filled with data of user and crs.
	 *
	 * @throws 	\InvalidArgumentException on unknown event, user or crs.
	 * @param type string subject or body
	 * @return string
	 */
	public function getMailPieces($a_which);
	
	/**
	 * Get the sender id from the amd config
	 *
	 * @return	int
	 */
	public function getSenderId();

	/**
	 * Return a course object
	 * @return object
	 */
	public function getCourse();

}