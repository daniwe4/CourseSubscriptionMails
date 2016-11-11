<?php

include_once("./Services/EventHandling/classes/class.ilEventHookPlugin.php");
require_once(__DIR__ . "/ilNaiveMailTemplating.php");
require_once(__DIR__ . "/ilMailSender.php");
require_once(__DIR__ . "/CourseSubscriptionMailsSettings.php");

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
	 * get the sender's user-id from settings
	 *
	 * @return 	int
	 */
	private function getSenderId() {
		/*
		DO NOT USE ilSetting LIKE THAT.
		IT will horribly reset global $ilSetting!

		$settings =  new ilSetting();
		$settings->ilSetting('xcsm'); //also reads.
		return (int)$settings->get('sender_id', 6);
		*/
		global $ilDB;
		$setting = array();
		$query = "SELECT * FROM settings WHERE module='xcsm'";
		$res = $ilDB->query($query);

		while ($row = $ilDB->fetchAssoc($res)) {
			$setting[$row["keyword"]] = $row["value"];
		}
		
		if(! $setting['sender_id']) {
			$setting['sender_id'] = 6;
		}
		return $setting['sender_id'];
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

			if ($a_component == "Modules/Course") {
				$settings = new Mails\classes\CourseSubscriptionMailsSettings($a_event);
				if($settings->isPluginEvent() && $settings->isCSMEnabled($a_parameter["obj_id"])) {
					global $ilLog;

					$ilLog->write(
						"Plugin.CSM.handleEvent"
						."\nhandled event: " .print_r($a_event, true) 
						."\n [usr_id: " .$a_parameter["usr_id"]
						.", obj_id: " .$a_parameter["obj_id"]
						."]"
						." -  sender_id (cfg): " .$this->getSenderId()
					);

					$mail_templating = new Mails\classes\ilNaiveMailTemplating(
						$a_event, 
						(int)$a_parameter["usr_id"], 
						(int)$a_parameter["obj_id"],
						(int)$this->getSenderId()
					);

					$mail_sender = new Mails\classes\ilMailSender((int)$a_parameter["usr_id"], (int)$a_parameter["obj_id"]);
					$mail_sender->sendMail($mail_templating, $settings);
			}
		}
	}
}
