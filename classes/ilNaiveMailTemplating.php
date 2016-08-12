<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailTemplate.php");
require_once(__DIR__ . "/../../../../../../../../Modules/Course/classes/class.ilObjCourse.php");
require_once(__DIR__ . "/../business/MailSettings.php");

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
		
		
		$settings = new Mails\business\MailSettings();
		return $settings->getMailText($this->event_name, $usr->getFirstName(), $crs->getTitle());

		// $tmp = $settings->getEventTextArray($this->event_name);

		// return $tmp[0] .$usr->getFirstName() . $tmp[1] .$crs->getTitle() . $tmp[2];		
	}
}