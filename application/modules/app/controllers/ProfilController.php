<?php

class ProfilController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
		$this->mUser = new Model_User();
	}

	public function indexAction()
	{
		if(isset($_POST['mail']))
		{
			$data['lastname'] = $_POST['lastname'];
			$data['firstname'] = $_POST['firstname'];
			$data['mail'] = $_POST['mail'];
			if(!empty($_POST['pass']) && Lib_Pass::encode($_POST['pass'])!=$this->session->user['pass'])
				$data['pass'] = $_POST['pass'];
			$this->mUser->update($data,$this->session->user['id']);
			$this->session->user = $this->mUser->get($this->session->user['id']);
		}
		$linkModel = new Model_Link();
		$this->view->links = $linkModel->findAllByAttributes(array('idUser'=>$this->session->user['id']));
	}
}