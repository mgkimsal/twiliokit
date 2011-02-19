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
	class TwilioRestResponse {
		
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
	
	/* TwilioRestClient throws TwilioRestException on error 
	 * Useful to catch this exception separately from general PHP
	 * exceptions, if you want
	 */
	class TwilioRestException extends Exception {}
	
	/*
	 * TwilioRestBaseClient: the core Rest client, talks to the Twilio REST 			
	 * API. Returns a TwilioRestResponse object for all responses if Twilio's 
	 * API was reachable Throws a TwilioRestException if Twilio's REST API was
	 * unreachable
	 */
	class TwilioRestClient {

		protected $Endpoint;
		protected $AccountSid;
		protected $AuthToken;
		
		/*
		 * __construct 
		 *   $username : Your AccountSid
		 *   $password : Your account's AuthToken
		 *   $endpoint : The Twilio REST Service URL, currently defaults to
		 * the proper URL
		 */
		public function __construct($accountSid, $authToken,
			$endpoint = "https://api.twilio.com") {
			
			$this->AccountSid = $accountSid;
			$this->AuthToken = $authToken;
			$this->Endpoint = $endpoint;
		}
		
		/*
		 * sendRequst
		 *   Sends a REST Request to the Twilio REST API
		 *   $path : the URL (relative to the endpoint URL, after the /v1)
		 *   $method : the HTTP method to use, defaults to GET
		 *   $vars : for POST or PUT, a key/value associative array of data to
		 * send, for GET will be appended to the URL as query params
		 */
		public function request($path, $method = "GET", $vars = array()) {

			$encoded = "";
			foreach($vars AS $key=>$value)
				$encoded .= "$key=".urlencode($value)."&";
			$encoded = substr($encoded, 0, -1);
			
			// construct full url
			$url = "{$this->Endpoint}/$path";
			
			// if GET and vars, append them
			if($method == "GET") 
				$url .= (FALSE === strpos($path, '?')?"?":"&").$encoded;

			// initialize a new curl object			
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			switch(strtoupper($method)) {
				case "GET":
					curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
					break;
				case "POST":
					curl_setopt($curl, CURLOPT_POST, TRUE);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
					break;
				case "PUT":
					// curl_setopt($curl, CURLOPT_PUT, TRUE);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
					file_put_contents($tmpfile = tempnam("/tmp", "put_"),
						$encoded);
					curl_setopt($curl, CURLOPT_INFILE, $fp = fopen($tmpfile,
						'r'));
					curl_setopt($curl, CURLOPT_INFILESIZE, 
						filesize($tmpfile));
					break;
				case "DELETE":
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
					break;
				default:
					throw(new TestException("Unknown method $method"));
					break;
			}
			
			// send credentials
			curl_setopt($curl, CURLOPT_USERPWD,
				$pwd = "{$this->AccountSid}:{$this->AuthToken}");
			
			// do the request. If FALSE, then an exception occurred	
			if(FALSE === ($result = curl_exec($curl)))
				throw(new TwilioRestException(
					"Curl failed with error " . curl_error($curl)));
			
			// get result code
			$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			// unlink tmpfiles
			if($fp)
				fclose($fp);
			if(strlen($tmpfile))
				unlink($tmpfile);
				
			return new TwilioRestResponse($url, $result, $responseCode);
		}
	}
?>