<?php

class BilbotController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
		$this->mLink = new Model_Link();
	}

	public function indexAction()
	{
		$this->view->links = $this->mLink->findAllByAttributes(array('idUser'=>$this->session->user['id'],'type'=>Model_Link::TYPE_TWITTER));
		
	}
}