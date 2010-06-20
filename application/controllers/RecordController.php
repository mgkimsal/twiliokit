<?php

class RecordController extends Zend_Controller_Action 
{

	protected function base()
	{
//		return "http://".$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getBaseUrl();
		return Zend_Registry::get("baseUrl");
	}

    public function init()
    {
	$this->session = new Zend_Session_Namespace("twilio");
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    }
	
	public function callAction()
	{
		$client = new Twilio_Rest_Client();
		$response = $client->call(
			array(
				"Caller"=>"9195339913",
				"Called"=>$this->_request->getParam('toPhone'),
				"Url"=>$this->base()."/record/record?name=".$this->_request->getParam('name')
			)
		);
		$this->view->dump = $response;
		$this->view->out = $this->_request->getParam('toPhone');
	}

	public function recordAction()
	{
		$t = new Twilio_Out();
		$t->say("Please leave a message.  Press the star key when finished");
		$t->record($this->base()."/record/capture",20,"*",4);
		$t->say("Sorry - I did not hear you.  ");
		$t->redirectGet($this->base()."/record/record");
		echo $t->out();
		die();
	}

	public function captureAction()
	{
		$url = $this->_request->getParam('RecordingUrl');
		$t = new Twilio_Out();
		$t->say("Thank you for your recording.  Here's what you said...");
		$t->play($url);
		$t->say("Goodbye!");
		$t->hangup();
		echo $t->out();
		die();
	}

	protected function logCall($array)
	{
		$c = new Tcall();
		$c->fromArray($array);
		$c->save();
	}
}


