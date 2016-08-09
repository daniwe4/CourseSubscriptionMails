<?php

/**
 * Interface that abstracts Email functions
 */
interface Mail {
	
	/**
	 * Sents a mail to User 
	 * 
	 * @param int 		$address
	 * @param string 	$subject
	 * @param string 	$message 
	 *
	 * @return null 
	 */
	public sendMail($address, $subject, $message);


}