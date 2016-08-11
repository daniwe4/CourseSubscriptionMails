<?php

namespace CaT\Plugins\CourseSubscriptionMails\business;

/**
 * Defines the settings for mail delivery depending on events
 */
class MailSettings {
	protected $event_name;
	protected $handled_events = array(
		"addSubscriber" => array("Hallo ", ", Sie haben sich erfolgreich in den Kurs \"", "\" eingeschrieben."),
		"addToWaitingList" => array("Hallo ", ", Sie hwurden erfolgreich der Warteliste für den Kurs \"", "\" hinzugefügt."),
		"deleteParticipant" => array("Hallo ", ", Sie wurden erfolgreich aus dem Kurs \"", "\" entfernt."),
		"deleteFromWaitingList" => array("Hallo ", ", Sie wurden aus der Warteliste des Kurses \"", "\" ausgetragen."),
		"moveUpOnWaitingList" => array("Hallo ", ", Sie sind in der Warteliste für den Kurs \"", "\" einen Platz nach oben gestiegen.")
		); 

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


		if(array_key_exists($event_name, $this->handled_events)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Returns an array, for concatinate a answer string
	 */
	public function getEventTextArray($event) {
			return $this->handled_events[$event_name];
	}
}