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

	public function getMailText($event, $name, $title) {
		$handled_events = array(
			"addSubscriber" => function($name, $title) { return "Hallo $name, Sie haben sich erfolgreich in den Kurs $title eingeschrieben."; },
			"addToWaitingList" => function($name, $title) { return "Hallo $name, Sie wurden erfolreich auf die Warteliste für den Kurs $title gesetzt."; },
			"deleteParticipant" => function($name, $title) { return "Hallo $name, Sie wurden erfolgreich aus dem Kurs $title entfernt."; },
			"deleteFromWaitingList" => function($name, $title) { return "Hallo $name, Sie wurden erfolgreich von der Warteliste des Kurses $title entfernt."; },
			"moveUpOnWaitingList" => function($name, $title) { return "Hallo $name, Sie sind in der Warteliste für den Kurs $title einen Platz nach oben gestiegen."; }
		);

		return $handled_events[$event]($name, $title);
		
	}
}