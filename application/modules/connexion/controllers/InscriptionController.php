<?php

class connexion_InscriptionController extends Lib_AC_ConnexionController
{	
    public function init()
    {
		parent::init();
		// On ne charge pas le layout de l'espace client
		$this->mUser = new Model_User();
    }

    public function indexAction()
    {
    	if(isset($_POST['mail']))
    	{
    		$idUser = $this->mUser->add($_POST);
    		$this->mUser->get($idUser);
            $this->_redirect('/');
    	}
    }
}
