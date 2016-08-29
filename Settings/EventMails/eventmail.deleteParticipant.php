<?php
//$genMailText  = function (\ilObjUser $user, \ilObjCourse $crs) {
function genMailText(\ilObjUser $user, \ilObjCourse $crs) {
	
	//customer specific:
	$mail_template = 'storno';
	//$mail_template = 'invite' | 'storno' | 'waiting' | 'waiting_cancel'

	require(dirname(__FILE__) .'/axa.lookupJILLDataForCourse.php'); //array $COURSEDESC


	$txt = $MAIL_TEMPLATE;
	$txt = str_replace('[VORNAME] [NACHNAME]', $user->getFullName(), $txt);
	$txt = str_replace('[DATUM]', $COURSEDESC['startdate'], $txt);
	$txt = str_replace('[STARTZEIT]', $COURSEDESC['courseStartTime'], $txt);
	$txt = str_replace('[ENDZEIT]', $COURSEDESC['courseEndTime'], $txt);

	if ($course_type == 'webinar') {

		//$txt = str_replace('[TITEL]', $COURSEDESC['TITLE'], $txt);
		$txt = str_replace('[TITEL]', $COURSEDESC['LONGTITLE'], $txt);
		$txt = str_replace('[RUFNUMMER-TEILNEHMER]', $user->getPhoneOffice(), $txt);
		$txt = str_replace('[INHALTE]', strip_tags($COURSEDESC['INHALTE']), $txt);
		$txt = str_replace('[PIN]', $COURSEDESC['CSN_PIN'], $txt);
		$txt = str_replace('[CSN_PHONE]', $COURSEDESC['CSN_FON'], $txt);
		$txt = str_replace('[CSN_LINK]', $COURSEDESC['CSN_LINK'], $txt);

	}
/*
	if ($course_type == 'f2f') {
		//pass
	}
*/
	
	return $txt;
}
?>