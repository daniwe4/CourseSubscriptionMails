<?php

include_once("./Services/EventHandling/classes/class.ilEventHookPlugin.php");
/**
*  Listen on Modules/Course events
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

	/**
	* Handle Modules/Course events
	*/
	final function handleEvent($a_component, $a_event, $a_parameter) {
		global $ilLog;

		$ilLog->write("a_component: " . $a_component . "----- a_event: " . print_r($a_event, true) . "------- a_parameter: " . print_r($a_parameter, true));


	}
}