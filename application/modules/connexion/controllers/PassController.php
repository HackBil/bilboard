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
            $pass = Lib_Pass::newpass();
            $data['pass'] = Lib_Pass::encode($pass);
            $this->mUser->update($data,$user['idElt']);
            mail($_POST['mail'],'BIL - Nouveau Mot de passe',$pass);
            $this->_redirect('/');
    	}
    }
}
