<?php

include_once(__DIR__ . "/../../../../../../../../Services/EventHandling/classes/class.ilEventHookPlugin.php");
require_once(__DIR__ . "/Mail/ilMailTemplating.php");
require_once(__DIR__ . "/Mail/ilMailSender.php");
require_once(__DIR__ . "/Mail/ilMailer.php");
require_once(__DIR__ . "/Mail/ICalGenerator.php");
require_once(__DIR__ . "/Mail/ICalGenerator_Null.php");

use CaT\Plugins\CourseSubscriptionMails\Mail;

/**
*  Listen on Modules/Course events
* 
* @author Daniel Weise
*/
class ilCourseSubscriptionMailsPlugin extends \ilEventHookPlugin {
	
	const PLUGIN_NAME = 'CourseSubscriptionMails';

	/**
	 * When a user is moved from the waiting list to the participants list,
	 * two events are created for the same user: addParticipant and
	 * removeFromWaitingList. This is bad, since a user should only get
	 * the message that he was actually added. We store the user that were
	 * added to prevent the second mail.
	 */
	protected $added_to_member_list = array();

	/**
	* Get Plugin Name. Must be same as in class name il<Name>Plugin
	* and must correspond to plugins subdirectory name.
	*
	* @return	string	Plugin Name
	*/
	final function getPluginName(){
		return self::PLUGIN_NAME;
	}


	/**
	 * Handle Modules/Course events
	 *
	 * @param 	string 	$a_component
	 * @param 	string 	$a_event
	 * @param 	array 	$a_parameter
	 *
	 * @return 	null
	 */
	final function handleEvent($a_component, $a_event, $a_parameter) {
		assert('is_string($a_component) === true');
		assert('is_string($a_event) === true');
		assert('is_array($a_parameter) === true');

		// Not a course? Go away.
		if ($a_component !== "Modules/Course") {
			return;
		}

		$mail_templating = new Mail\ilMailTemplating($a_event, (int)$a_parameter["usr_id"], (int)$a_parameter["obj_id"]);

		if($mail_templating->isPluginEvent() && $mail_templating->isCSMEnabled()) {
			global $ilLog;

			if ($a_event == "addParticipant") {
				$this->added_to_member_list[] = $a_parameter["usr_id"];
			}
			else if ($a_event == "removeFromWaitingList") {
				// Read comment for added_to_member_list.
				if (in_array($a_parameter["usr_id"], $this->added_to_member_list)) {
					return;
				}
			}

			$ilLog->write(
				"Plugin.CSM.handleEvent"
				."\nhandled event: " .print_r($a_event, true) 
				."\n [usr_id: " .$a_parameter["usr_id"]
				.", obj_id: " .$a_parameter["obj_id"]
				."]"
			);

			$usr = new \ilObjUser((int)$a_parameter["usr_id"]);
			$from = new \ilObjUser($mail_templating->getSenderId());
			$mail_from = new Mail\ilMailer($from);
			$mail_recipient = new Mail\ilMailer($usr);
			$iCal = new Mail\ICalGenerator_Null($mail_templating);

			$mail_sender = new Mail\ilMailSender();
			$mail_sender->sendMail($iCal, $mail_templating, $mail_recipient, $mail_from);
		}
		
	}
}
