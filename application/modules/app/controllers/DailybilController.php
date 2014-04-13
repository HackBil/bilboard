<?php

class DailybilController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
		$this->mLink = new Model_Link();
	}

	public function indexAction()
	{
		$this->view->categories = Lib_Dailybil_Api::categories();
		$this->view->accounts = $this->mLink->findByAttributes(array('idUser'=>$this->session->user['id']));
	}
}