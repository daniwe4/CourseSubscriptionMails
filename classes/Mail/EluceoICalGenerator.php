<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

require_once(__DIR__ . "/ilMailTemplating.php");
require_once(__DIR__ . "/ICalGenerator.php");
require_once(__DIR__ . "/../../../../../../../../../Services/Mail/phpmailer/class.phpmailer.php");
require_once(__DIR__ . "/../../../../../../../../../Modules/Course/classes/class.ilObjCourse.php");



/**
 * Generates an email attachment file in iCal format
 * 
 * @author Daniel Weise
 * 
 */
class EluceoICalGenerator implements ICalGenerator {
	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $dt_start;

	/**
	 * @var string
	 */
	private $dt_end;

	/**
	 * @var string
	 */
	private $location;

	/**
	 * @var string
	 */
	private $organizer;

	private $tplFile;
	private $tplPath;

	public function __construct(MailTemplate $a_nmtpl) {
		
		$this->crs = $a_nmtpl->getCourse();
		$this->nmtpl = $a_nmtpl;

		$this->tplFile = "tpl.csm_iCal.html";
		$this->tplPath = "Customizing/global/plugins/Services/EventHandling/EventHook/CourseSubscriptionMails/";

		$this->replacePlaceholders($this->tpl);
	}

	/**
	 * set standard template or if set via setTplPath()
	 * and setTplFile() the specified template file
	 *
	 * @param string $a_file
	 * @param string $a_path
	 * @return object ilTemplate
	 */
	protected function getTpl($a_file, $a_path) {
		require_once("./Services/UICore/classes/class.ilTemplateHTMLITX.php");
		require_once("./Services/UICore/classes/class.ilTemplate.php");

		list($file, $path) = $this->getFileAndPath($a_file, $a_path);

		return new \ilTemplate($file, false, true, $path);
	}

	/**
	 * get the standard template path if one param is empty
	 * @param string $a_file
	 * @param string $a_path
	 * @return array
	 */
	protected function getFileAndPath($a_file, $a_path) {
		assert('is_string($a_file)');
		assert('is_string($a_path)');

		if(empty($a_file) || empty($a_path)) {
			return ['tpl.csm_iCal.html', 'Customizing/global/plugins/Services/EventHandling/EventHook/CourseSubscriptionMails'];
		} else {
			return [$a_file, $a_path];
		}

	}

	/**
	 * set the path to template file
	 * @param string $value
	 */
	public function setTplPath($value) {
		assert('is_string($value');

		$this->tplPath = $value;
	}

	/**
	 * set the template file name
	 * should be a html file
	 * @param  string $value
	 */
	public function setTplFile($value) {
		assert('is_string($value)');

		$this->tplFile = $value;
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
	 * Returns a course object
	 * 
	 * @return object
	 */
	public function getCourse() {
		if(is_object($this->crs)) {
			return $this->crs;
		}
		return null;
	}

	/**
	 * Returns course start start date in format:
	 * 
	 * jjjj-mm-dd
	 * 
	 * @return date
	 */
	public function getCourseStartDate() {
		if($this->getCourse()->getCourseStart() != null) {
			return $this->getCourse()->getCourseStart()->get(IL_CAL_DATE);
		}
		return "";
	}

	/**
	 * Returns course end date in format:
	 * 
	 * jjjj-mm-dd
	 * 
	 * @return date
	 */
	public function getCourseEndDate() {
		if($this->getCourse()->getCourseEnd() != null) {
			return $this->getCourse()->getCourseEnd()->get(IL_CAL_DATE);
		}
		return "";
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
		return $this->getCourseEndDate() . " 23:59:00";
	}
	

	/**
	 * Generate an email attachment in iCal format
	 * 
	 * @return array|null
	 */
	public function buildICal() {
		require_once(__DIR__ . "/../../vendor/autoload.php");

		//setup iCal
		$calendar = new \Eluceo\iCal\Component\Calendar('meinelernumgebung');

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

		$a_tpl = $this->getTpl($this->tplFile, $this->tplPath);
		$placeholders = $a_tpl->getBlockvariables("DTStart");
		$this->dt_start = $this->buildIcalBlock
							( "DTStart"
							, $this->nmtpl->parsePlaceholders($placeholders)
							);

		$placeholders = $a_tpl->getBlockvariables("DTEnd");
		$this->dt_end = $this->buildIcalBlock
							( "DTEnd"
							, $this->nmtpl->parsePlaceholders($placeholders)
							);

		$placeholders = $a_tpl->getBlockvariables("Location");
		$this->location = $this->buildIcalBlock
							( "Location"
							, $this->nmtpl->parsePlaceholders($placeholders)
							);

		$placeholders = $a_tpl->getBlockvariables("Description");
		$this->description = $this->buildIcalBlock
							( "Description"
							, $this->nmtpl->parsePlaceholders($placeholders)
							);

		$placeholders = $a_tpl->getBlockvariables("Organizer");
		$this->organizer = $this->buildIcalBlock
							( "Organizer"
							, $this->nmtpl->parsePlaceholders($placeholders)
							);
	}

	/**
	 * Replaces placeholders for one block in a template file.
	 * 
	 * @param string $which name of the template block 
	 * @param strting $tpl_file path+filname
	 * @param array $vars placeholder variables
	 * @return string the hole block content with replaced placeholders
	 */
	public function buildIcalBlock($which, $vars) {
		assert('is_string($which)');
		assert('is_array($vars)');

		if(!empty($which) && is_array($vars)) {
			//$tpl = new \ilTemplate($tpl_file, true, true, "Customizing/global/plugins/Services/EventHandling/EventHook/CourseSubscriptionMails/");
			$tpl_file = $this->getTpl($this->tplFile, $this->tplPath);
			$tpl_file->setCurrentBlock($which);
			foreach ($vars as $key => $value) {
				$tpl_file->setVariable($key, $value);
			}
			$tpl_file->setVariable("DO_NOT_DELETE", "");

			$tpl_file->parseCurrentBlock();
		} else {
			global $ilLog;
			$ilLog->write("Error on building iCal for CSM: " . $e);
			return "";
		}
		return trim($tpl_file->get());
	}
}
