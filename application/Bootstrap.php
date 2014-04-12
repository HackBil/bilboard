<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{ 
	public function run()
	{
		// Cela permet d'avoir la configuration disponible de partout dans notre application
		Zend_Registry::set('config', new Zend_Config($this->getOptions()));
		parent::run();
	}

	protected function _initAutoload()
	{
		Zend_Loader_Autoloader::getInstance()->registerNamespace('Thumbnailer_')->getRegisteredNamespaces();

		// On enregistre les modules (les parties de notre application), souvenez-vous : Backend et Frontend
		$loader = new Zend_Application_Module_Autoloader(array(
		'namespace' => '',
		'basePath'  => APPLICATION_PATH));

		return $loader;
	}

	protected function _initSession()
	{
	
		// On initialise la session
		$session = new Zend_Session_Namespace('session', true);
		Zend_Registry::set('session', $session);
		return $session;
	}

	protected function _initView()
	{
		// Initialisation de la vue et des helpers de vue
		$view = new Zend_View();
		$view->setEncoding('utf-8');
		$view->doctype('XHTML1_STRICT');
		// On ajoute le dossier des helpers
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers');
		// On charge l'helper qui va se charger de la vue
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);
		return $view;
	}
	protected function _initRouter()
	{
		$this->bootstrap('frontController');
		$frontController = $this->getResource('frontController');

		define('MODULE_PATH',APPLICATION_PATH.'/modules/');
		$frontController->addModuleDirectory(MODULE_PATH);
	
		// Chargement des routes dans le fichier config.ini
		$configRoutes = new Zend_Config_Ini(APPLICATION_PATH.'/configs/router.ini', null);
		$router = new Zend_Controller_Router_Rewrite();
		$router->addConfig($configRoutes);
		$frontController->setRouter($router);		
		
		// Initialisation du gestionnaire d'erreurs.
		Zend_Controller_Front::getInstance()
		->registerPlugin(
			new Zend_Controller_Plugin_ErrorHandler(
				array(
					'module'     => 'error',
					'controller' => 'Error',
					'action'     => 'error'
				)   
			)   
		);
	}
	
	protected function _initTranslate(){
		$translate = new Zend_Translate('csv', APPLICATION_PATH .'/languages/french.csv', 'fr');
		//$translate->addTranslation(APPLICATION_PATH .'/languages/english.csv', 'en');
		$session = Zend_Registry::get('session');
		$langLocale = isset($session->lang) ? $session->lang : 'fr';

		try{
			$translate->setLocale($langLocale);
		}catch(Zend_Exception $e){
			$translate->setLocale('fr');
		}
		Zend_Registry::set('Zend_Translate', $translate);
	}
	
	protected function _initOverload()
	{
		// Prise en compte du dossier Zendlucas pour surcharger les classes de Zend
		$autoloader  = Zend_Loader_Autoloader::getInstance();
		$autoloader -> registerNamespace('Lib_');
	}

	protected function _initRestRoute()
	{
		$this->bootstrap('frontController');
		$frontController = Zend_Controller_Front::getInstance();
		$restRoute = new Zend_Rest_Route($frontController ,array(), array('default' => array('api_IncidentController' )));
		$frontController->getRouter()->addRoute('rest', $restRoute);
	}
}	