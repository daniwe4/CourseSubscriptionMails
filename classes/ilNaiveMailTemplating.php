<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;



require_once(__DIR__ . "/../interfaces/MailTemplate.php");
require_once("./Modules/Course/classes/class.ilObjCourse.php");
require_once(__DIR__ . "/CourseSubscriptionMailsSettings.php");


use CaT\Plugins\CourseSubscriptionMails as Mails;

class ilNaiveMailTemplating implements Mails\interfaces\MailTemplate {
	protected $event_name;
	protected $usr_id;
	protected $crs_id;
	private $sender_id;

	
	public function __construct($a_event_name, $a_usr_id, $a_crs_id, $a_sender_id) {
		$this->setEventName($a_event_name);
		$this->setUserId($a_usr_id);
		$this->setCourseId($a_crs_id);
		$this->setSenderId($a_sender_id);

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
	 * set sender id
	 *
	 * @param 	int 	$a_id
	 * @return
	 *
	 */
	private function setSenderId($a_id) {
		assert(is_int($a_id) && $a_id > 0);
		$this->sender_id = $a_id;
	}

	/**
	 * get sender id
	 *
	 * @return 	int
	 *
	 */
	public function getSenderId() {
		return $this->sender_id;
	}

	/**
	 * @inheritdoc
	 */
	public function getMessage() {
		
		$usr = new \ilObjUser($this->getUserId());
		$crs = new \ilObjCourse($this->getCourseId(), false);
		$mail_settings = new CourseSubscriptionMailsSettings($this->getEventName());

		switch ($this->getEventName()) {
			case 'addParticipant':
				return $mail_settings->getMailHtml($usr, $crs, "addParticipant", "BODY");

			case 'addToWaitingList':
				return $mail_settings->getMailHtml($usr, $crs, "addToWaitingList", "BODY");

			case 'deleteParticipant':
				return $mail_settings->getMailHtml($usr, $crs, "deleteParticipant", "BODY");

			case 'removeFromWaitingList':
				return $mail_settings->getMailHtml($usr, $crs, "removeFromWaitingList", "BODY");

			case 'remindDueCourse':
				return $mail_settings->getMailHtml($usr, $crs, "remindDueCourse", "BODY");
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getSubject() {
		$usr = new \ilObjUser($this->getUserId());
		$crs = new \ilObjCourse($this->getCourseId(), false);
		$mail_settings = new CourseSubscriptionMailsSettings($this->getEventName());
		
		switch ($this->getEventName()) {
			case 'addParticipant':
				return $mail_settings->getMailHtml($usr, $crs, "addParticipant", "SUBJECT");

			case 'addToWaitingList':
				return $mail_settings->getMailHtml($usr, $crs, "addToWaitingList", "SUBJECT");

			case 'deleteParticipant':
				return $mail_settings->getMailHtml($usr, $crs, "deleteParticipant", "SUBJECT");

			case 'removeFromWaitingList':
				return $mail_settings->getMailHtml($usr, $crs, "removeFromWaitingList", "SUBJECT");	

			case 'remindDueCourse':
				return $mail_settings->getMailHtml($usr, $crs, "remindDueCourse", "SUBJECT");

		}

	}
}
