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
		if(isset($_POST['categories']))
		{
			$link = $this->mLink->findByAttributes(array('title'=>$_POST['title'],'idUser'=>$this->session->user['id'],'type'=>Model_Link::TYPE_TWITTER));
			$_POST['connectionObject'] = $link['connectionObject'];
			Lib_Bilbot_Api::create($_POST);
		}
		$this->view->links = $this->mLink->findAllByAttributes(array('idUser'=>$this->session->user['id'],'type'=>Model_Link::TYPE_TWITTER));
	}
}