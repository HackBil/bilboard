<?php

class ProfilController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
	}

	public function indexAction()
	{
		$linkModel = new Model_Link();
		$this->view->links = $linkModel->findAllByAttributes(array('user_id'=>$this->session->user['id']));
		
	}
}