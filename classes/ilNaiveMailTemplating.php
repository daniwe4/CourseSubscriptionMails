<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailTemplate.php");
require_once(__DIR__ . "/../../../../../../../../Modules/Course/classes/class.ilObjCourse.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;

class ilNaiveMailTemplating implements Mails\interfaces\MailTemplate {

	/**
	 * @inheritdoc
	 */
	public function getMailFor($event_name, $user_id, $crs_id) {

		$user = new \ilObjUser($user_id);
		$crs = new \ilObjCourse($crs_id);
		
		switch ($event_name) {
			case 'addSubscriber':
				return "Hallo " .$user->username ."<br /><br />sie haben sich erfolgreich in den Kurs " .$crs->crs_name ." eingeschrieben.";
				break;
			
			case 'addToWaitingList':
				return "Hallo " .$user->username ."<br /><br />sie haben sich erfolgreich der Warteliste für den Kurs " .$crs->crs_name ." hinzugefügt.";
				break;

			case 'deleteParticipant':
				return "Hallo " .$user->username ."<br /><br />sie wurden erfolgreich aus dem Kurs " .$crs->crs_name ." entfernt.";
				break;

			case 'deleteFromWaitingList':
				return "Hallo " .$user->username ."<br /><br />sie wurden erfolgreich von der Warteliste des Kurses " .$crs->crs_name ." entfernt.";
				break;

			case 'moveUpOnWaitingList':
				return "Hallo " .$user->username ."<br /><br />sie sind in der Warteliste für den Kurs " .$crs->crs_name ." einen Platz nach oben gestiegen.";
				break;
				
			default:
				// throw new \InvalidArgumentException("Event not known", 1);
				// break;

		
		}
	}
}