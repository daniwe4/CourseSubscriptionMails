<?php
namespace CaT\Plugins\CourseSubscriptionMails\interfaces;

use CaT\Plugins\CourseSubscriptionMails;


interface MailTemplate {


	/**
	 * Instantiate with event-name, user-id and course-id;
	 * you can always change those params using the setters 
	 * setEventName, setUserId, setCourseId
	 *
	 * @param 	string 	$a_event_name
	 * @param 	int 	$a_usr_id
	 * @param 	int 	$a_crs_id
	 *
	 * @throws 	\InvalidArgumentException on unknown event, user or crs.
	 * @return 	null 
	 */
	public function __construct($a_event_name, $a_usr_id, $a_crs_id);

	
	/**
	 * Get the appropriate mail for the given event, where placeholders are filled
	 * with data of user and crs.
	 *
	 * @throws 	\InvalidArgumentException on unknown event, user or crs.
	 * @return	string
	 */
	public function getMessage();
	

	/**
	 * Get the appropriate mail-subject for the given event, user and course
	 *
	 * @return	string
	 */
	public function getSubject();

	
	/**
	 * Get the appropriate attachments for the given event, user and course
	 *
	 * @return	array
	 */
	public function getAttachments();

}