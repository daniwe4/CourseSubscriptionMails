<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

class ICalGenerator_Null implements MailICalGenerator {
	public function buildICal() {
		return null;
	}
}

