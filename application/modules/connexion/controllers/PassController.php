<?php

class connexion_PassController extends Lib_AC_ConnexionController
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
            $user = $this->mUser->getFromMail($_POST['mail']);
            $data['id'] = $user['idElt'];
            $pass = Lib_Pass::new();
            $data['pass'] = Lib_Pass::encode($pass);
            $this->mUser->update($data);
            mail($_POST['mail'],'BIL - Nouveau Mot de passe',$pass);
            $this->_redirect('/');
    	}
    }
}
