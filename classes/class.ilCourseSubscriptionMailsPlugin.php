<?php

include_once("./Services/EventHandling/classes/class.ilEventHookPlugin.php");

/**
 *  Listen on course subscriptors
 */
class ilCourseSubscriptionMailsPlugin extends ilEventHookPlugin {
	
	/**
	* Get Plugin Name. Must be same as in class name il<Name>Plugin
	* and must correspond to plugins subdirectory name.
	*
	* @return	string	Plugin Name
	*/
	final function getPluginName(){
		return "CourseSubscriptionMails";
	}

	final function handleEvent($a_component, $a_event, $a_parameter) {
		global $ilLog;

		$ilLog->write("<-------------- Neuer Eintrag --------------->");
	}
}