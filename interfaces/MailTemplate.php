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
	public function __construct($a_event_name, $a_usr_id, $a_crs_id, $a_sender_id);

	
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
	 * Get the sender id from config
	 *
	 * @return	int
	 */
	public function getSenderId();

}