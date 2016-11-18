<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

/**
 * Wrap an ilObjUser object to get only fullname and email address
 */
interface MailICalGenerator {
	public function buildICal();
}