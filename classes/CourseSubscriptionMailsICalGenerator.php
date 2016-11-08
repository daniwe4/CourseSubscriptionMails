<?php

require_once("Services/User/classes/class.ilObjUser.php");
require_once("Modules/Course/classes/class.ilObjCourse.php");
require_once(__DIR__ . "/ilNaiveMailTemplating.php");
require_once("Services/Mail/phpmailer/class.phpmailer.php");

use CaT\Plugins\CourseSubscriptionMails\interfaces as Mails;

class CourseSubscriptionMailsICalGenerator {
	public function __construct($crs_id, $usr_id, Mails\MailTemplate $nmtpl) {
		$this->crs_id = $crs_id;
		$this->usr_id = $usr_id;
		$this->nmtpl = $nmtpl;
	}
	
	public function getCourse() {
		if($this->crs === null) {
			$this->crs = new ilObjCourse($this->crs_id, false);
		}
		return $this->crs;
	}
	
	public function getCourseStartDate() {
		if($this->crs === null) {
			$this->getCourse();
			return $this->crs->getCourseStart()->get(IL_CAL_DATE);
		}
		return $this->crs->getCourseStart()->get(IL_CAL_DATE);
	}
	
	public function getCoursEndDate() {
		if($this->crs === null) {
			$this->getCourse();
			return $this->crs->getCourseEnd()->get(IL_CAL_DATE);
		}
		return $this->crs->getCourseEnd()->get(IL_CAL_DATE);
	}
	
	public function getCourseStartDateTime() {
		return $this->getCourseStartDate() . " 00:01";
	}
	
	public function getCourseEndDatetime() {
		return $this->getCoursEndDate() . " 23:59";
	}
	
	public function getMailDescription() {
		$tmp = new \PHPMailer();
		return $tmp->html2text($this->nmtpl->getMessage());
	}
	
	public function getMailSummary() {
		return $this->nmtpl->getSubject();
	}
	
	public function buildICal() {
		require_once(__DIR__ . "/../vendor/autoload.php");

		//setup iCal
		$calendar = new \Eluceo\iCal\Component\Calendar('makler-akademie.de');

		$tz_rule_daytime = new \Eluceo\iCal\Component\TimezoneRule(\Eluceo\iCal\Component\TimezoneRule::TYPE_DAYLIGHT);
		$tz_rule_daytime
			->setTzName('CEST')
			->setDtStart(new \DateTime('1981-03-29 02:00:00', $dtz))
			->setTzOffsetFrom('+0100')
			->setTzOffsetTo('+0200');
		$tz_rule_daytime_rec = new \Eluceo\iCal\Property\Event\RecurrenceRule();
		$tz_rule_daytime_rec
			->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY)
			->setByMonth(3)
			->setByDay('-1SU');
		$tz_rule_daytime->setRecurrenceRule($tz_rule_daytime_rec);
		$tz_rule_standart = new \Eluceo\iCal\Component\TimezoneRule(\Eluceo\iCal\Component\TimezoneRule::TYPE_STANDARD);
		$tz_rule_standart
			->setTzName('CET')
			->setDtStart(new \DateTime('1996-10-27 03:00:00', $dtz))
			->setTzOffsetFrom('+0200')
			->setTzOffsetTo('+0100');
		$tz_rule_standart_rec = new \Eluceo\iCal\Property\Event\RecurrenceRule();
		$tz_rule_standart_rec
			->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY)
			->setByMonth(10)
			->setByDay('-1SU');
		$tz_rule_standart->setRecurrenceRule($tz_rule_standart_rec);
		$tz = new \Eluceo\iCal\Component\Timezone('Europe/Berlin');
		$tz->addComponent($tz_rule_daytime);
		$tz->addComponent($tz_rule_standart);
		$calendar->setTimezone($tz);
		
		
		$event = new \Eluceo\iCal\Component\Event();
		$event
			->setDtStart(new \DateTime($this->getCourseStartDateTime()))
			->setDtEnd(new \DateTime($this->getCourseEndDatetime()))
			->setNoTime(false)
			->setLocation("","")
			->setUseTimezone(true)
			->setSummary($this->getMailSummary())
			->setDescription($this->getMailDescription())
			->setOrganizer(new \Eluceo\iCal\Property\Event\Organizer(""));
		
		$calendar
			->setTimezone($tz)
			->addComponent($event);
		
		//sending user: 
		$sender_id = $this->nmtpl->getSenderId();
		$ilMailer = new ilMail((int)$sender_id);
		
		$cal_file_name ='iCalEntry.ics';
		$cal_file_path = 
			$ilMailer->mfile->getMailPath()
			.'/'
			.$sender_id
			.'_'
			.$cal_file_name;

		if(file_exists($cal_file_path)) {
			unlink($cal_file_path);
		}

		$wstream = fopen($cal_file_path,"w");
		fwrite($wstream, $calendar->render());
		fclose($wstream);

		$attachments = array($cal_file_path);

		return $attachments;
		
	}
}