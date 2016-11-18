<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

interface ICalGenerator {

	/**
	 * generates a file called "iCalEntry.ics" in the
	 * configuered data directory during ILIAS installation
	 * 
	 * @return array|null
	 */
	public function buildICal();
}