<?php

class SmsController extends Zend_Controller_Action 
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
		$response = $client->sms(
			array(
				"From"=>"9195339913",
				"To"=>$this->_request->getParam('toPhone'),
				"Body"=>$this->_request->getParam('message'),
			)
		);
		$this->view->dump = $response;
		$this->view->out = $this->_request->getParam('toPhone');
		$this->view->message = $this->_request->getParam('message');
	}


	protected function logCall($array)
	{
		$c = new Tcall();
		$c->fromArray($array);
		$c->save();
	}
}


