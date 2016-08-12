<?php
use CaT\Plugins\CourseSubscriptionMails as Mails;

class SendCorrectMailToUserTest extends PHPUnit_Framework_TestCase {
	protected $mockMailSender;
	protected $mockMailTemplate;
	protected $testClass;
	protected $right_usr_id 	= 285;
	protected $right_subject 	= "testmail";
	protected $right_crs_id 	= 2;
	protected $right_message 	= "test_ok";
	protected $false_usr_id 	= -2;
	protected $false_subject 	= "falscherwert";
	protected $false_message 	= "test_not_ok";
	protected $false_crs_id 	= -2;
	protected $event_arr 		= array("addSubscriber", "addParticipant", "addToWaitingList", "deleteParticipant");


	public function setUp() {

		require_once(__DIR__ . "/mockMailSender.php");
		$this->mockMailSender = new mockMailSender();

		require_once(__DIR__ . "/mockMailTemplate.php");
		$this->mockMailTemplate = new mockMailTemplate();

		require_once(__DIR__ . "/../business/SendCorrectMailToUser.php");
		$this->testClass = new Mails\business\SendCorrectMailToUser($this->mockMailTemplate, $this->mockMailSender);

		
	}
	
	public function test_business() {
		foreach($this->event_arr as $event) {
			$this->testClass->sendCorrectMail($event, $this->right_usr_id, $this->right_crs_id);

			/**
			 * mockMailSenderTests
			 */
			$this->assertEquals($this->mockMailSender->message, $this->right_message);
			$this->assertEquals($this->mockMailSender->subject, $this->right_subject);

			/**
			 * mockMailTemplateTests
			 */
			$this->assertEquals($this->mockMailTemplate->event_name, $event);
			$this->assertEquals($this->mockMailTemplate->usr_id, $this->right_usr_id);
			$this->assertEquals($this->mockMailTemplate->crs_id, $this->right_crs_id);

			/**
			 * negative mockMailSenderTests
			 */
			$this->assertFalse($this->mockMailSender->message == $this->false_message);
			$this->assertFalse($this->mockMailSender->subject == $this->false_subject);

			/**
			 * mockMailTemplateTests
			 */
			$event = "no_event";
			$this->assertFalse($this->mockMailTemplate->event_name == $event);
			$this->assertFalse($this->mockMailTemplate->usr_id == $this->false_usr_id);
			$this->assertFalse($this->mockMailTemplate->crs_id == $this->false_crs_id);
		}
		
	}
}