<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailTemplate.php");
require_once(__DIR__ . "/../../../../../../../../Modules/Course/classes/class.ilObjCourse.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;

class ilNaiveMailTemplating implements Mails\interfaces\MailTemplate {
	protected $event_name;
	protected $user_id;
	protected $crs_id;
	/**
	 * @inheritdoc
	 */
	public function getMailFor($event_name, $user_id, $crs_id) {
		assert(is_string($event_name));
		assert(is_int($user_id));
		assert(is_int($crs_id));

		$this->event_name = $event_name;
		$this->user_id = $user_id;
		$this->crs_id = $crs_id;
		die(var_dump($crs_id));
		$user = new \ilObjUser($this->user_id);
		$crs = new \ilObjCourse($this->crs_id);
		$crs->read();
		
		switch ($this->event_name) {
			case 'addSubscriber':
				return "Hallo " .$user->getFirstName() ."<br /><br />sie haben sich erfolgreich in den Kurs " .$crs->getTitle() ." eingeschrieben.";
				break;
			
			case 'addToWaitingList':
				return "Hallo " .$user->getFirstName() ."<br /><br />sie haben sich erfolgreich der Warteliste für den Kurs " .$crs->getTitle() ." hinzugefügt.";
				break;

			case 'deleteParticipant':
				return "Hallo " .$user->getFirstName() ."<br /><br />sie wurden erfolgreich aus dem Kurs " .$crs->getTitle() ." entfernt.";
				break;

			case 'deleteFromWaitingList':
				return "Hallo " .$user->getFirstName() ."<br /><br />sie wurden erfolgreich von der Warteliste des Kurses " .$crs->getTitle() ." entfernt.";
				break;

			case 'moveUpOnWaitingList':
				return "Hallo " .$user->getFirstName() ."<br /><br />sie sind in der Warteliste für den Kurs " .$crs->getTitle() ." einen Platz nach oben gestiegen.";
				break;
				
			default:
				// throw new \InvalidArgumentException("Event not known", 1);
				// break;

		}
		
	}
}