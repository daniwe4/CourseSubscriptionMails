<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

/**
 * Use this class instead of EluecoICalGenerator to
 * disable the generation of ICal files.
 */
class NullEluceoICalGenerator implements ICalGenerator {
	
	/**
	 * @return array
	 */
	public function buildICal() {
		return null;
	}
}

