<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

require_once(__DIR__ . "/ilMailTemplating.php");
//require_once(__DIR__ . "/class.PluginConfig.php");
require_once("Services/Mail/phpmailer/class.phpmailer.php");
require_once("Services/User/classes/class.ilObjUser.php");
require_once("Modules/Course/classes/class.ilObjCourse.php");


/**
 * Generates an email attachment file in iCal format
 * 
 * @author Daniel Weise
 * 
 */
class ICalGenerator {
	private $description;
	private $dt_start;
	private $dt_end;
	private $location;
	private $organizer;

	public function __construct($a_crs_id, $a_usr_id, Mails\MailTemplate $nmtpl) {
		assert(is_int($a_crs_id) === true);
		assert(is_int($a_usr_id) === true);

		$this->crs_id = $crs_id;
		$this->usr_id = $usr_id;
		$this->nmtpl = $nmtpl;
		$this->replacePlaceholders();
	}

	/**
	 * Returns a course object
	 * 
	 * @return object
	 */
	public function getCourse() {
		if($this->crs === null) {
			$this->crs = new \ilObjCourse($this->crs_id, false);
		}
		return $this->crs;
	}
	
	/**
	 * Returns an user object
	 * 
	 * @return object
	 */
	public function getUser() {
		if($this->usr === null) {
			$this->usr = new \ilObjUser($this->user_id);
		}
		return $this->usr;
	}

	/**
	 * Returns course start start date in format:
	 * 
	 * jjjj-mm-dd
	 * 
	 * @return date
	 */
	public function getCourseStartDate() {
		if($this->crs === null) {
			$this->getCourse();
			return $this->crs->getCourseStart()->get(IL_CAL_DATE);
		}
		return $this->crs->getCourseStart()->get(IL_CAL_DATE);
	}

	/**
	 * Returns course end date in format:
	 * 
	 * jjjj-mm-dd
	 * 
	 * @return date
	 */
	public function getCoursEndDate() {
		if($this->crs === null) {
			$this->getCourse();
			return $this->crs->getCourseEnd()->get(IL_CAL_DATE);
		}
		return $this->crs->getCourseEnd()->get(IL_CAL_DATE);
	}

	/**
	 * Returns course start date with time in format:
	 * 
	 * jjjj-mm-dd hh:mm:ss
	 * 
	 * @return date
	 */
	public function getCourseStartDateTime() {
		if($this->dt_start !== "" && preg_match('#(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})#',  $this->dt_start)){
			return $this->dt_start;
		}
		return $this->getCourseStartDate() . " 00:01:00";
	}

	/**
	 * Returns course end date with time in format:
	 * 
	 * jjjj-mm-dd hh:mm:ss
	 * 
	 * @return date
	 */
	public function getCourseEndDatetime() {
		if($this->dt_end !== "" && preg_match('#(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})#', $this->dt_end)) {
			return $this->dt_end;
		}
		return $this->getCoursEndDate() . " 23:59:00";
	}


	/**
	 * Returns a description for the mail
	 * 
	 * @return string
	 */
	public function getDescription() {
		if($this->description) {
			return $this->description;
		}
		return "";
	}

	/**
	 * Returns the name of the organizer from the course
	 * 
	 * @return string
	 */
	public function getOrganizer() {
		if($this->organizer !== "") {
			return $this->organizer;
		}
		return "";
	}

	/**
	 * Returns the location where the course takes place
	 * 
	 * @return string
	 */
	public function getLocation() {
		if($this->location !== "") {
			return $this->location;
		}
		return "";
	}
	

	/**
	 * Generate an email attachment in iCal format
	 * 
	 * @return array
	 */
	public function buildICal() {
		require_once(__DIR__ . "/../vendor/autoload.php");

		//setup iCal
		// TODO: The string passed here needs to be dynamic, not every ical value
		// is created by medicproof.de.
		$calendar = new \Eluceo\iCal\Component\Calendar('meinelernumgebung.medicproof.de');

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
			->setLocation($this->getLocation(),"")
			->setUseTimezone(true)
			->setSummary($this->getCourse()->getTitle())
			->setDescription($this->getDescription())
			->setOrganizer(new \Eluceo\iCal\Property\Event\Organizer($this->getOrganizer()));
		
		$calendar
			->setTimezone($tz)
			->addComponent($event);
		
		//sending user: 
		$sender_id = $this->nmtpl->getSenderId();
		$ilMailer = new \ilMail((int)$sender_id);
		
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

	/**
	 * Loads a template file and replaces placeholder for different blocks
	 * 
	 * @return null
	 */
	private function replacePlaceholders() {
		$placeholders = array();
		
		$tpl_file = "./Customizing/global/skin/MailTemplates/tpl.csm_iCal.html";
		$tpl = new \ilTemplate($tpl_file, true, true);

		$placeholders = $tpl->getBlockvariables("DTStart");
		$this->dt_start = $this->buildIcalBlock
							( "DTStart"
							, $tpl_file
							, $this->nmtpl->parsePlaceholders($placeholders, $this->getUser(), $this->getCourse())
							);

		$placeholders = $tpl->getBlockvariables("DTEnd");
		$this->dt_end = $this->buildIcalBlock
							( "DTEnd"
							, $tpl_file
							, $this->nmtpl->parsePlaceholders($placeholders, $this->getUser(), $this->getCourse())
							);

		$placeholders = $tpl->getBlockvariables("Location");
		$this->location = $this->buildIcalBlock
							( "Location"
							, $tpl_file
							, $this->nmtpl->parsePlaceholders($placeholders, $this->getUser(), $this->getCourse())
							);

		$placeholders = $tpl->getBlockvariables("Description");
		$this->description = $this->buildIcalBlock
							( "Description"
							, $tpl_file
							, $this->nmtpl->parsePlaceholders($placeholders, $this->getUser(), $this->getCourse())
							);

		$placeholders = $tpl->getBlockvariables("Organizer");
		$this->organizer = $this->buildIcalBlock
							( "Organizer"
							, $tpl_file
							, $this->nmtpl->parsePlaceholders($placeholders, $this->getUser(), $this->getCourse())
							);
	}

	/**
	 * Description
	 * @param string $which name of the template block 
	 * @param strting $tpl_file path+filname
	 * @param array $vars placeholder variables
	 * @return string the hole block content with replaced placeholders
	 */
	private function buildIcalBlock($which, $tpl_file, $vars) {

		try{
			$tpl = new \ilTemplate($tpl_file, true, true);
			$tpl->setCurrentBlock($which);
			foreach ($vars as $key => $value) {
				$tpl->setVariable($key, $value);
			}
			$tpl->setVariable("DO_NOT_DELETE", "");

			$tpl->parseCurrentBlock();
		}
		catch (Exception $e) {
			global $ilLog;
			$ilLog->write("Error on building iCal for CSM: " . $e);
			return "";
		}

		return trim($tpl->get());
	}
}
