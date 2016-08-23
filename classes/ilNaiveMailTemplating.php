<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

require_once(__DIR__ . "/../interfaces/MailTemplate.php");
require_once("./Modules/Course/classes/class.ilObjCourse.php");
require_once(__DIR__ . "/MailSettings.php");


use CaT\Plugins\CourseSubscriptionMails as Mails;

class ilNaiveMailTemplating implements Mails\interfaces\MailTemplate {
	protected $event_name;
	protected $usr_id;
	protected $crs_id;

	
	public function __construct($a_event_name, $a_usr_id, $a_crs_id) {
		$this->setEventName($a_event_name);
		$this->setUserId($a_usr_id);
		$this->setCourseId($a_crs_id);
	}

	/**
	 * set event-name 
	 *
	 * @param 	string 	$a_event_name
	 * @return null 
	 *
	 */
	public function setEventName($a_event_name) {
		assert(is_string($a_event_name));
		$this->event_name = $a_event_name;
	}

	/**
	 * get current event-name 
	 *
	 * @return 	string 	
	 *
	 */
	public function getEventName() {
		return $this->event_name;
	}

	/**
	 * set user-id 
	 *
	 * @param 	int 	$a_usr_id
	 * @return 	null 
	 *
	 */
	public function setUserId($a_usr_id) {
		assert(is_int($a_usr_id) && $a_usr_id >= 0);
		$this->usr_id = $a_usr_id;
	}

	/**
	 * get current user-id
	 *
	 * @return 	int 	
	 *
	 */
	public function getUserId() {
		return $this->usr_id;
	}

	/**
	 * set course-id
	 *
	 * @param 	int 	$a_crs_id
	 * @return 	null 
	 *
	 */
	public function setCourseId($a_crs_id) {
		assert(is_int($a_crs_id) && $a_crs_id > 0);
		$this->crs_id = $a_crs_id;
	}

	/**
	 * get current course-id 
	 *
	 * @return 	int 	
	 *
	 */
	public function getCourseId() {
		return $this->crs_id;
	}





	/**
	 * @inheritdoc
	 */
	public function getMessage() {
		$usr = new \ilObjUser($this->getUserId());
		$crs = new \ilObjCourse($this->getCourseId(), false);
				
		$settings = new Mails\classes\MailSettings();
/*
		$builder = $settings->getMailTextBuilder($this->getEventName());
		return $builder($usr, $crs);
*/

		$tpath = dirname(__FILE__) .'/../Settings/EventMails/';
		switch ($this->getEventName()) {
			case 'addParticipant':
				require_once($tpath .'eventmail.addParticipant.php');
				return genMailText($usr, $crs);
				break;

			case 'addToWaitingList':
				require_once($tpath .'eventmail.addToWaitingList.php');
				return genMailText($usr, $crs);
				break;

			case 'deleteParticipant':
				require_once($tpath .'eventmail.deleteParticipant.php');
				return genMailText($usr, $crs);
				break;

			case 'removeFromWaitingList':
				require_once($tpath .'eventmail.removeFromWaitingList.php');
				return genMailText($usr, $crs);
				break;

		}


	}

	/**
	 * @inheritdoc
	 */
	public function getSubject() {

		switch ($this->getEventName()) {
			case 'addParticipant':
				return 'Buchungsbestätigung Ihres Seminars';
				break;

			case 'addToWaitingList':
				return 'Buchung auf Warteliste';
				break;

			case 'deleteParticipant':
				return 'Absage Ihrer Seminarteilnahme';
				break;

			case 'removeFromWaitingList':
				return 'Abmeldung von Warteliste';
				break;

		}

	}

	/**
	 * @inheritdoc
	 */
	public function getAttachments() {
		$usr = new \ilObjUser($this->getUserId());
		$crs = new \ilObjCourse($this->getCourseId(), false);
		$settings = new Mails\classes\MailSettings();

		$tpath = dirname(__FILE__) .'/../Settings/EventMails/';
		switch ($this->getEventName()) {
			case 'addParticipant':
				require_once($tpath .'attachment.addParticipant.php');
				return genMailAttachments($usr, $crs, $this);
				break;

		}

		//$builder = $settings->getAttachmentBuilder($this->getEventName());
		//return $builder($usr, $crs, $this);

	}


}