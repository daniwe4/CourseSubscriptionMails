<?php
use CaT\Plugins\CourseSubscriptionMails\Mail;

require_once('./Services/Xml/classes/class.ilXmlWriter.php');
require_once('./Services/Calendar/classes/class.ilDate.php');
require_once('./Modules/Course/classes/class.ilObjCourse.php');
require_once(__DIR__ . '/../classes/Mail/ilMailSender.php');
require_once(__DIR__ . '/../classes/Mail/ilMailTemplating.php');
require_once(__DIR__ . '/../classes/Mail/EluceoICalGenerator.php');
require_once(__DIR__ . '/../classes/Mail/ilMailer.php');

class ilMailSenderTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		require_once("Services/Context/classes/class.ilContext.php");
		require_once("Services/Init/classes/class.ilInitialisation.php");
		\ilContext::init(\ilContext::CONTEXT_UNITTEST);
		\ilInitialisation::initILIAS();

		// ilcal - Mock
		$mock_iCal = $this->getMockBuilder('\CaT\Plugins\CourseSubscriptionMails\Mail\EluceoICalGenerator')
						 ->disableOriginalConstructor()
						 ->getMock();

		$mock_iCal->method("buildICal")
				  ->will($this->returnValue(["test.ical"]));//returnCallback($this, 'getFiles'));

		// iilMailer Mock
		$mock_mailer = $this->getMockBuilder('\CaT\Plugins\CourseSubscriptionMails\Mail\ilMailer')
						 	->disableOriginalConstructor()
						 	->getMock();

		$mock_mailer->method('getFullname')
				 	->will($this->returnValue("Eins Zwei"));

		$mock_mailer->method('getEmailAddress')
				 	->will($this->returnValue("test@testmail.de"));

		// MailTemplate - Mock
		$mock_tpl = $this->getMockBuilder('\CaT\Plugins\CourseSubscriptionMails\Mail\ilMailTemplating')
						 ->disableOriginalConstructor()
						 ->getMock();

		$mock_tpl->method('getMailPieces')
				 ->with($this->isType('string'))
				 ->will($this->returnValue('MailPiece'));

		$mock_tpl->method('getSenderId')
				 ->will($this->returnValue('6'));

		$mock_tpl->method('getCourse')
				 ->will($this->returnValue($mock_crs));

		$mock_tpl->method('parsePlaceholders')
				 ->with($this->isType('array'))
				 ->will($this->returnValue('DO_NOT_DELETE'));

		$this->ilMailSender = new Mail\ilMailSender($mock_iCal, $mock_tpl, $mock_mailer, $mock_mailer);
		// $this->mt = new Mail\ilMailTemplating("addPaticipant", 6, 296);
	}

	public function getFiles() {
		$files = ["test.iCal"];
		return $files;
	}
	
	public function test_ilMailSender() {
		// TODO: implement my_ilMailSender and test on object
	}
}

class my_ilMailSender extends ilMailSender {
	public function test_buildPHPMail() {
		return $this->buildPHPMail();
	}
	public function test_buildMail($a_tpl) {
		return $this->buildMail($a_tpl);
	}
}