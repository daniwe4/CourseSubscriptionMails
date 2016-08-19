<?php
$genMailAttachments  = function (\ilObjUser $user, \ilObjCourse $crs) {
	require_once(__DIR__ . "/../../vendor/autoload.php");


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





	require_once("./Services/AXA/Utils/classes/class.axaCourseUtils.php");
	$cutils = axaCourseUtils::getInstance($crs->getId(), axaCourseUtils);

	$crs_startdate = $crs->getCourseStart()->get(IL_CAL_DATE);
	$crs_starttime = $cutils->getCourseStartTime();
	$crs_endtime = $cutils->getCourseEndTime();
	$crs_location = $cutils->getCourseLocation();

	$start_date = $crs_startdate .' ' .$crs_starttime .':00';
	$end_date = $crs_startdate .' ' .$crs_endtime .':00';

	$event = new \Eluceo\iCal\Component\Event();
	$event
		->setDtStart(new \DateTime($start_date))
		->setDtEnd(new \DateTime($end_date))
		->setNoTime(false)
		->setUseTimezone(true)
		->setSummary($crs->getTitle())
		->setLocation($crs_location,$crs_location);
		//->setDescription($this->getSubtitle())
		//->setOrganizer(new \Eluceo\iCal\Property\Event\Organizer($organizer));

	$calendar
		->setTimezone($tz)
		->addComponent($event);



	$user_id = $user->getId();
	$ilMailer = new ilMail($user_id);


	//if(!file_exists($this->mail_path.'/'.$this->user_id.'_'.$file))
	$cal_file_name ='iCalEntry.ics';

	$cal_file_path = 
		$ilMailer->mfile->getMailPath()
		.'/'
		.$user_id
		.'_'
		.$cal_file_name;

	if(file_exists($cal_file_path)) {
		unlink($cal_file_path);
	}

	$wstream = fopen($cal_file_path,"w");
	fwrite($wstream, $calendar->render());
	fclose($wstream);
	
	$attachments = array($cal_file_name);

	
	return $attachments;
}

?>