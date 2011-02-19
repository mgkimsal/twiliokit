<?php

	/* TwilioRestClient throws TwilioRestException on error 
	 * Useful to catch this exception separately from general PHP
	 * exceptions, if you want
	 */
	class Twilio_Rest_Exception extends Exception {}
	
?>
