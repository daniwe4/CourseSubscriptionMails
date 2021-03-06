<?php
namespace CaT\Plugins\CourseSubscriptionMails\interfaces;

use CaT\Plugins\CourseSubscriptionMails;


interface MailTemplate {
	/**
	 * Get the appropriate mail for the given event, where placeholders are filled
	 * with data of user and crs.
	 *
	 * @param 	string 	$event_name
	 * @param 	int 	$user_id
	 * @param 	int 	$crs_id
	 *
	 * @throws 	\InvalidArgumentException on unknown event, user or crs.
	 * @return	string
	 */
	public function getMailFor($event_name, $user_id, $crs_id);
}