<?php

class connexion_IndexController extends Lib_AC_ConnexionController
{	
    public function init()
    {
		parent::init();
		// On ne charge pas le layout de l'espace client
		$this->mClient = new Model_Client();

		// Nécessaire pour gérer les cookies
		$requestCookies = new Zend_Controller_Request_Http();
		$this->remember = $requestCookies->getCookie('remember');
    }

    public function indexAction()
    {
		if(isset($this->remember) && $this->remember!="")
		{
			$this->session->client = $this->mClient->get($this->remember);
		}
		if(isset($_POST['mail']))
		{
			$idClient = $this->mClient->login($_POST['mail'],$_POST['pass']);
			$this->session->client = $this->mClient->get($idClient);
		}
		if((isset($_POST['remember']) && $_POST['remember']=="true") || (isset($this->remember) && $this->remember!=""))
		{
			setcookie('remember', $idClient , (time() + 3600*24*7),"/","bilboard.fr");
		}
        if(isset($this->session->client))
            $this->_redirect('/');
    }
}