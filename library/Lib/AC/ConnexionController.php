<?php

class Lib_AC_ConnexionController extends Zend_Controller_Action
{
	public function init()
	{
        if (Zend_Registry::isRegistered('config'))
            $this->config = Zend_Registry::get('config');
        else
            $this->config = null;
        // Gestion de la session
		if(Zend_Registry::isRegistered('session'))
        {
            $this->session = Zend_Registry::get('session');
			if($this->config != null)
				$this->session->setExpirationSeconds($this->config->duree_session*3600);
        }
        $this->view->session = $this->session;
		//layout
		$this->_helper->layout->setLayout('layout_connexion');

	}
}