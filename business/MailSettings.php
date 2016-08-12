<?php

namespace CaT\Plugins\CourseSubscriptionMails\business;

/**
 * Defines the settings for mail delivery depending on events
 */
class MailSettings {
	protected $event_name;

	protected $possible_events = array("addSubscriber", "addToWaitingList", "deleteParticipant", "deleteFromWaitingList", "moveUpOnWaitingList");


	/**
	 * Returns true if the Event is handled by the plugin 
	 * otherwise false
	 * 
	 * @param 	string 	$event_name
	 *
	 * @return 	boolean
	 */
	public function isPluginEvent($event_name) {
		assert(is_string($event_name));

		$this->event_name = $event_name;

		if(in_array($event_name, $this->possible_events)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function getMailTextBuilder($event) {
		$handled_events = array(
			"addSubscriber" => function(\ilObjUser $user, \ilObjCourse $crs) {
					return "Hallo ".$user->getFullName().", Sie haben sich erfolgreich in den Kurs ".$crs->getTitle()." eingeschrieben.";
			},
			"addToWaitingList" => function(\ilObjUser $user, \ilObjCourse $crs) { 
				return "Hallo ".$user->getFullName().", Sie wurden erfolreich auf die Warteliste für den Kurs ".$crs->getTitle()." gesetzt."; 
			},
			"deleteParticipant" => function(\ilObjUser $user, \ilObjCourse $crs) { 
				return "Hallo ".$user->getFullName().", Sie wurden erfolgreich aus dem Kurs ".$crs->getTitle()." entfernt."; 
			},
			"deleteFromWaitingList" => function(\ilObjUser $user, \ilObjCourse $crs) { 
				return "Hallo ".$user->getFullName().", Sie wurden erfolgreich von der Warteliste des Kurses ".$crs->getTitle()." entfernt."; 
			},
			"moveUpOnWaitingList" => function(\ilObjUser $user, \ilObjCourse $crs) { 
				return "Hallo ".$user->getFullName().", Sie sind in der Warteliste für den Kurs ".$crs->getTitle()." einen Platz nach oben gestiegen."; 
			}
		);

		return $handled_events[$event];
	}
}