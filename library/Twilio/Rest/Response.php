<?php
    /*
    Copyright (c) 2008 Twilio, Inc.

    Permission is hereby granted, free of charge, to any person
    obtaining a copy of this software and associated documentation
    files (the "Software"), to deal in the Software without
    restriction, including without limitation the rights to use,
    copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following
    conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
    HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
    WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
    OTHER DEALINGS IN THE SOFTWARE.
    */
    
	// ensure Curl is installed
	if(!extension_loaded("curl"))
		throw(new Exception(
			"Curl extension is required for TwilioRestClient to work"));
	
	/* 
	 * TwilioRestResponse holds all the REST response data 
	 * Before using the reponse, check IsError to see if an exception 
	 * occurred with the data sent to Twilio
	 * ResponseXml will contain a SimpleXml object with the response xml
	 * ResponseText contains the raw string response
	 * Url and QueryString are from the request
	 * HttpStatus is the response code of the request
	 */
	class Twilio_Rest_Response {
		
		public $ResponseText;
		public $ResponseXml;
		public $HttpStatus;
		public $Url;
		public $QueryString;
		public $IsError;
		public $ErrorMessage;
		
		public function __construct($url, $text, $status) {
			preg_match('/([^?]+)\??(.*)/', $url, $matches);
			$this->Url = $matches[1];
			$this->QueryString = $matches[2];
			$this->ResponseText = $text;
			$this->HttpStatus = $status;
			if($this->HttpStatus != 204)
				$this->ResponseXml = @simplexml_load_string($text);
			
			if($this->IsError = ($status >= 400))
				$this->ErrorMessage =
					(string)$this->ResponseXml->RestException->Message;
			
		}
		
	}
	

?>
