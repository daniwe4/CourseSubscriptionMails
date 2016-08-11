<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailTemplate.php");
require_once(__DIR__ . "/../../../../../../../../Modules/Course/classes/class.ilObjCourse.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;

class ilNaiveMailTemplating implements Mails\interfaces\MailTemplate {
	protected $event_name;
	protected $usr_id;
	protected $crs_id;

	/**
	 * @inheritdoc
	 */
	public function getMailFor($event_name, $usr_id, $crs_id) {
		assert(is_string($event_name));
		assert(is_int($usr_id) && $usr_id >= 0);
		assert(is_int($crs_id) && $crs_id > 0);
		
		$this->event_name = $event_name;
		$this->usr_id = $usr_id;
		$this->crs_id = $crs_id;

		$usr = new \ilObjUser($this->usr_id);
		$crs = new \ilObjCourse($this->crs_id, false);
		
		
		switch ($this->event_name) {
			case 'addSubscriber':
				return "Hallo " .$usr->getFirstName() ." Sie haben sich erfolgreich in den Kurs \"" .$crs->getTitle() ."\" eingeschrieben.";
							
			case 'addToWaitingList':
				return "Hallo " .$usr->getFirstName() ." Sie hwurden erfolgreich der Warteliste für den Kurs \"" .$crs->getTitle() ."\" hinzugefügt.";
				
			case 'deleteParticipant':
				return "Hallo " .$usr->getFirstName() ." Sie wurden erfolgreich aus dem Kurs \"" .$crs->getTitle() ."\" entfernt.";
				
			case 'deleteFromWaitingList':
				return "Hallo " .$usr->getFirstName() ." Sie wurden aus der Warteliste des Kurses \"" .$crs->getTitle() ."\" ausgetragen.";
				
			case 'moveUpOnWaitingList':
				return "Hallo " .$usr->getFirstName() ." Sie sind in der Warteliste für den Kurs \"" .$crs->getTitle() ."\" einen Platz nach oben gestiegen.";
								
			default:
				// throw new \InvalidArgumentException("Event not known", 1);
				// break;

		}
		
	}
}