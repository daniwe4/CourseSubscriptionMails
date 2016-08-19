<?php

namespace CaT\Plugins\CourseSubscriptionMails\classes;

/**
 * Defines the settings for mail delivery depending on events
 */
class MailSettings {
	protected $event_name;

	protected $possible_events = array(
		//"addSubscriber", 
		"addParticipant", 
		"addToWaitingList",
		"deleteParticipant", 
		"removeFromWaitingList", 
	//	"moveUpOnWaitingList"
	);


	/**
	 * Returns true if the Event is handled by the plugin 
	 * otherwise false
	 * 
	 * @param 	string 	$event_name
	 *
	 * @return 	boolean
	 */
	public function isPluginEvent($event_name) {
		assert(is_string($event_name));

		$this->event_name = $event_name;

		if(in_array($event_name, $this->possible_events)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * true, if strings begins with search
	 *
	 * @param 	string 	$string
	 * @param 	string 	$search
	 *
	 * @return 	boolean
	 */
	private	function startswith ($string, $search) {
	   return (substr($string, 0, strlen($search)) === $search);
	}



	/**
	 * Read all files in Settings/EventMails.
	 * If filename begins with "eventmail.",
	 * register the remaining part of the filenme as template
	 *
	 * files must contain ONE function "genMailText" with
	 * user-object and crs-object as parameters:
	 * 		$genMailText  = function (\ilObjUser $user, \ilObjCourse $crs) 
	 * 
	 * @param 	string 	$event
	 *
	 * @return 	string
	 */

	public function getMailTextBuilder($event) {
		
		$handled_events = array();

		$tpath = dirname(__FILE__) .'/../Settings/EventMails/';
		foreach (glob($tpath .'*.php') as $templatefile) {
            $template_event_name =  basename($templatefile, '.php');
            if($this->startswith($template_event_name, 'eventmail.')){
            	$template_event_name = str_replace('eventmail.', '', $template_event_name);
            	require $templatefile;
            	$handled_events[$template_event_name] = $genMailText;
            }
         }

		return $handled_events[$event];
	}


	public function getAttachmentBuilder($event) {
		

		if($event === 'addParticipant') {
			$templatefile = dirname(__FILE__) .'/../Settings/EventMails/attachment.addParticipant.php';
			require $templatefile;
			return  $genMailAttachments;

		} else {
			$no_func = function($usr, $crs){};
			return $no_func;
		}
		
	}



}
