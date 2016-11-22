<?php
use CaT\Plugins\CourseSubscriptionMails\Mail;

require_once('./Services/Xml/classes/class.ilXmlWriter.php');
require_once('./Services/Calendar/classes/class.ilDate.php');
require_once('./Modules/Course/classes/class.ilObjCourse.php');
require_once(__DIR__ . '/../classes/Mail/ilMailTemplating.php');
require_once(__DIR__ . '/../classes/Mail/EluceoICalGenerator.php');

class iCalTest extends PHPUnit_Framework_TestCase {
	private $testDate ;

	protected function getTestDate() {
		return $this->testDate;
	}

	protected function setTestDate($value) {
		$this->testDate = $value;
	}

	public function setUp() {
		require_once("Services/Context/classes/class.ilContext.php");
		require_once("Services/Init/classes/class.ilInitialisation.php");
		\ilContext::init(\ilContext::CONTEXT_UNITTEST);
		\ilInitialisation::initILIAS();

		// ilDate - Mock
		$mock_dat = $this->getMockBuilder('ilDate')
						 ->disableOriginalConstructor()
						 ->getMock();

		$mock_dat->method("get")
				 ->will($this->returnCallback(function() {return $this->getTestDate(); }));

		// ilObjCourse Mock
		$mock_crs = $this->getMockBuilder('ilObjCourse')
						 ->disableOriginalConstructor()
						 ->getMock();

		$mock_crs->method('getCourseStart')
				 ->will($this->returnValue($mock_dat));

		$mock_crs->method('getCourseEnd')
				 ->will($this->returnValue($mock_dat));

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

		$this->iCal = new Mail\EluceoICalGenerator($mock_tpl);
		$this->testICal = new myICal($mock_tpl);
	}
	
	public function test_iCal() {

		$this->setTestDate("1998-01-01");
		// CourseStartDate test
		$this->assertEquals($this->iCal->getCourseStartDate(), $this->getTestDate());
		$this->assertNotEquals($this->iCal->getCourseStartDate(), "1999-01-01");
		// CourseEndDate test
		$this->assertEquals($this->iCal->getCourseEndDate(), $this->getTestDate());
		$this->assertNotEquals($this->iCal->getCourseEndDate(), "1999-01-01");
		
		$this->assertEquals($this->testICal->test_getFileAndPath("test1", "test2"), ['test1', 'test2']);
	}
}

class myICal extends Mail\EluceoICalGenerator {
	public function test_getFileAndPath($file, $path) {
		return $this->getFileAndPath($file, $path);
	}
}