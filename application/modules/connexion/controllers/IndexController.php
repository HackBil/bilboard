<?php

class connexion_IndexController extends Lib_AC_ConnexionController
{	
    public function init()
    {
		parent::init();
		// On ne charge pas le layout de l'espace client
		$this->mUser = new Model_User();

		// Nécessaire pour gérer les cookies
		$requestCookies = new Zend_Controller_Request_Http();
		$this->remember = $requestCookies->getCookie('remember');
    }

    public function indexAction()
    {
		if(isset($this->remember) && $this->remember!="")
		{
			$this->session->user = $this->mUser->get($this->remember);
		}
		if(isset($_POST['mail']))
		{
			$idClient = $this->mUser->login($_POST['mail'],$_POST['pass']);
			$this->session->user = $this->mUser->get($idClient);
		}
		if((isset($_POST['remember']) && $_POST['remember']=="true") || (isset($this->remember) && $this->remember!=""))
		{
			setcookie('remember', $idClient , (time() + 3600*24*7),"/","bilboard.fr");
		}
        if(isset($this->session->user))
        {
        	if($this->session->user['activ'])
            	$this->_redirect('/');
            else
            {
				unset($this->session->user);
            	$this->_redirect('/connexion/index/inactiv');
            }
        }
    }

    public function inactivAction()
    {}

	public function deconnexionAction()
	{
		// On supprime la session.
		Zend_Session::forgetMe();
		Zend_Session::destroy(true, true);
		Zend_Session::expireSessionCookie();
		
		//Supprime le cookie d'enregistrement de session
		setcookie('remember', '', (time() - 3600*24*365),"/","bilboard.fr");
		$this->_redirect('/');	
	}
}