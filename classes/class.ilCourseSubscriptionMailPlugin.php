<?php
	include_once("./Services/EventHandling/classes/class.ilEventHookPlugin.php");

	class ilCourseSubscriptionMailsPlugin() extends ilEventHookPlugin{
		final function getPluginName(){
			return "CourseSubscriptionMails";
		}
		private function init(){
			
		}
	}