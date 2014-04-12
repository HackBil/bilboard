<?php
// Définition des PATHs.
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('LIBRARY_PATH')
	|| define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));	
defined('ZEND_PATH')
	|| define('ZEND_PATH', realpath(dirname(__FILE__) . '/../library'));
defined('APPLICATION_PUBLIC')
	|| define('APPLICATION_PUBLIC', realpath(dirname(__FILE__)));
// defined('DOMAIN')
// 	|| define('DOMAIN', $_SERVER['SERVER_NAME']);
defined('DOMAIN')
	|| define('DOMAIN', "app.resideclic.com");

if($_SERVER['SERVER_NAME']=="resideclic.dev") // Dev ou localhost
{
        define('APPLICATION_ENV', 'development');
}elseif($_SERVER['SERVER_NAME'] == "dev.resideclic.com"){
        define('APPLICATION_ENV', 'preprod');
}else{
        defined('APPLICATION_ENV')
                || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
}

// On définie l'encodage pour les fonctions multi-bytes.
mb_internal_encoding ('UTF-8');

// On modifie l'include path de PHP
set_include_path( implode( PATH_SEPARATOR, array(realpath(ZEND_PATH), get_include_path(), ) ) );

// On a besoin de Zend Application pour lancer notre application
require_once 'Zend/Application.php';

// On lance la session
require_once 'Zend/Session.php';
Zend_Session::start();
// On créé l'application, on lance le bootstrap et on lance l'application !
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
$application->run();

