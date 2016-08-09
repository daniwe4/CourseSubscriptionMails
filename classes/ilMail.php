<?php

/**
 * 
 */
class ilMail implements Mail {
	protected $userID;
	protected $subject;
	protected $message;

	public function __construct($userID, $subject, $message) {
		$this->userID = $userID;
		$this->subject = $subject;
		$this->message = $message; 
	}

	@innerhitdoc
	protected function sendMail() {
				
	}
}	