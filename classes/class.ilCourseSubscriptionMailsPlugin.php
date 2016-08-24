<?php

include_once("./Services/EventHandling/classes/class.ilEventHookPlugin.php");
require_once(__DIR__ . "/ilNaiveMailTemplating.php");
require_once(__DIR__ . "/ilMailSender.php");
require_once(__DIR__ . "/MailSettings.php");
//require_once(__DIR__ . "/../business/SendCorrectMailToUser.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;
/**
*  Listen on Modules/Course events
*/
class ilCourseSubscriptionMailsPlugin extends ilEventHookPlugin {
	
	const PLUGIN_NAME = 'CourseSubscriptionMails';

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

 		$settings = new Mails\classes\MailSettings();

 		global $ilLog;
 		$ilLog->write(
 			"handle event: " .print_r($a_event, true) 
 			." [usr_id: " .$a_parameter["usr_id"]
 			.", obj_id: " .$a_parameter["obj_id"]
 			."]"
 			);

		if ($a_component == "Modules/Course" && $settings->isPluginEvent($a_event)) {

			$mail_templating = new Mails\classes\ilNaiveMailTemplating(
				$a_event, 
				(int)$a_parameter["usr_id"], 
				(int)$a_parameter["obj_id"]
			);

			$mail_sender = new Mails\classes\ilMailSender();

			$mail_sender->sendMail($mail_templating);
 			$ilLog->write("\n  ->Mail sent.");

		}
	}
}