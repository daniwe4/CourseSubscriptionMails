<?php

/**
 * Mock for MailSender
 */
class mockMailSender implements MailSender {
	public $usr_id = null;
	public $subject = null;
	public $message = null;

	public function __construct($usr_id, $subject, $message) {
		$this->usr_id = $usr_id;
		$this->subject = $subject;
		$this->message = $message;
	}

	public function usr_id() {
		return $this->usr_id;
	}

	public function subject() {
		return $this->subject;
	}

	public function message() {
		return $this->message;
	}

	/**
	 * @inerhitdoc
	 */
	public function sendMail($usr_id, $subject, $message) {
		response = array($usr_id, $subject, $message);
		return response;
	}
}