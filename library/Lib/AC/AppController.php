<?php

class Lib_AC_AppController extends Zend_Controller_Action
{
	public function init()
	{
        if (Zend_Registry::isRegistered('config'))
            $this->config = Zend_Registry::get('config');
        else
            $this->config = null;

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $messages = $this->flashMessenger->getMessages();
        if (!empty($messages))
        {
            $this->view->flash   = $messages;
            $this->flashMessenger->clearMessages();
        }
                        
        // Gestion de la session
		if(Zend_Registry::isRegistered('session'))
        {
            $this->session = Zend_Registry::get('session');
			if($this->config != null)
				$this->session->setExpirationSeconds($this->config->duree_session*3600);
        }
        if(!isset($this->session->user))
            $this->_redirect('/connexion');
        $this->view->session = $this->session;
        
		//layout
		$this->_helper->layout->setLayout('layout_app');

	}
}