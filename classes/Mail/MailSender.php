<?php
namespace CaT\Plugins\CourseSubscriptionMails\Mail;

/**
 * Interface to abstract Email functions
 */
interface MailSender {
	/**
	 * Description
	 * @param MailTemplate $a_template
	 * @param Mailer $a_recipient
	 * @param Mailer $a_sender
	 * @return null
	 */
	public function sendMail(ICalGenerator $a_iCal, MailTemplate $a_template, Mailer $a_recipient, Mailer $a_sender);

}