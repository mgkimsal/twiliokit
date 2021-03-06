<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function _initDoctrine() { 

		$this->getApplication()->getAutoloader()->pushAutoloader(array('Doctrine', 'autoload'));
		spl_autoload_register(array('Doctrine','modelsAutoload'));
		$manager = Doctrine_Manager::getInstance();

		$config = $this->getOption('doctrine');
		Doctrine_Core::setModelsDirectory($config['models_path']);
		$connection = Doctrine_Manager::connection($config['dsn'], 'doctrine');

		$profiler = new Doctrine_Connection_Profiler();

		$connection->setListener($profiler);

	}

	public function _initView()
	{   
		$view = new Zend_View();
		$view->doctype('XHTML1_STRICT');
		$view->env = APPLICATION_ENV;
		$config = new Zend_Config_Xml(APPLICATION_PATH."/configs/menu.xml", "nav");
		$nav = new Zend_Navigation($config);
		$view->navigation($nav);
		$render = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$render->setView($view);
		/*
		$c = $nav->findAllByController('index');
		$a = $nav->findAllByAction("index");
		$found = array_intersect($c, $a);
		 */
		return $view;
	} 

	public function _initBase()
	{
// need a better way - $_SERVER data isn't available in phpunit mode - config vars?
//              Zend_Registry::set("baseUrl","http://".$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getBaseUrl());
					Zend_Registry::set("baseUrl","http://zfkit.com/twilio/public".Zend_Controller_Front::getInstance()->getBaseUrl());
	}

	public function _initTwilio()
	{
					$config = $this->getOption("twilio");
					Zend_Registry::set("twilioBaseUrl", $config['endpoint']."/Accounts/".$config['accountSid']);
					Zend_Registry::set("twilioConfig", $config);
					$w = new Zend_Log_Writer_Stream($config['outlog']);
					$log = new Zend_Log($w);
					Zend_Registry::set("twiliolog", $log);
	}
	
/**
 * if you want true zend-framework 'rest'-style URLs
 * with all that entails, uncomment this method
 */

/*
	public function _initRouting()
	{

		$this->bootstrap('frontController');
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();
		$restRoute = new Zend_Rest_Route($front);
		$front->getRouter()->addRoute('rest', $restRoute);
	}
*/

}

