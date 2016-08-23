<?php
//$genMailAttachments  = function (\ilObjUser $user, \ilObjCourse $crs, \ilNaiveMailTemplating $templating) {
//$genMailAttachments  = function (\ilObjUser $user, \ilObjCourse $crs, $templating) {

function genMailAttachments (\ilObjUser $user, \ilObjCourse $crs, $templating) {
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




	$mail_template = 'invite';
	//$mail_template = 'invite' | 'storno' | 'waiting' | 'waiting_cancel'
	require(dirname(__FILE__) .'/axa.lookupJILLDataForCourse.php'); //array $COURSEDESC

	$crs_startdate = $crs->getCourseStart()->get(IL_CAL_DATE);
	$crs_starttime = $COURSEDESC['courseStartTime'];
	$crs_endtime = $COURSEDESC['courseEndTime'];
	$crs_location = $COURSEDESC['LOCATION'];


	$crs_title = $COURSEDESC['TITLE'] 
		.' (' 
		. $COURSEDESC['SUBTITLE'] 
		.')';

/*
	$crs_description = ''
		.strip_tags($COURSEDESC['ZIELE'])
		."\n\n"
		.strip_tags($COURSEDESC['INHALTE']);
*/
	
	//get mail-text	
	$crs_description = $templating->getMessage();

	$crs_description = str_replace("\r\n", '', $crs_description);
	$crs_description = str_replace("\n", '\\n', $crs_description);
	$crs_description = nl2br($crs_description);
	
	/*  also:
 		vendor/eluceo/ical/src/Eluceo/iCal/Util/PropertyValueUtil::escapeValueAllowNewLine
		comment out first replace
		
 		 //$value = str_replace('\\', '\\\\', $value);
        $value = str_replace('"', '\\"', $value);
	*/
		

	$crs_organizer = $COURSEDESC['PROVIDER'];

	$start_date = $crs_startdate .' ' .$crs_starttime .':00';
	$end_date = $crs_startdate .' ' .$crs_endtime .':00';

	$event = new \Eluceo\iCal\Component\Event();
	$event
		->setDtStart(new \DateTime($start_date))
		->setDtEnd(new \DateTime($end_date))
		->setNoTime(false)
		->setUseTimezone(true)
		->setSummary($crs_title)
		->setLocation($crs_location,$crs_location)
		->setDescription($crs_description)
		->setOrganizer(new \Eluceo\iCal\Property\Event\Organizer($crs_organizer));

	$calendar
		->setTimezone($tz)
		->addComponent($event);



	//$user_id = $user->getId();

	//sending user: use the fix support user
	$user_id = 6;
	$user_id = 3566; //dev
	$user_id = 4380; //live

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
