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
		if(isset($_POST['categories']))
		{
			$link = $this->mLink->findByAttributes(array('title'=>$_POST['title'],'idUser'=>$this->session->user['id'],'type'=>Model_Link::TYPE_TWITTER));
			$_POST['connectionObject'] = $link['connectionObject'];
			Lib_Dailybil_Api::create($_POST);
		}
		$links = $this->mLink->findAllByAttributes(array('idUser'=>$this->session->user['id'],'type'=>Model_Link::TYPE_TWITTER));
		$this->view->categories = Lib_Dailybil_Api::categories();
		
		$searches = array();		
		foreach($links as $link)
		{
			$searches[$link['title']]= Lib_Dailybil_Api::searches($link['connectionObject']);
		}
		$this->view->searches = $searches;
		$this->view->links = $links;
	}

	public function previewAction()
	{
		$this->_helper->layout->disableLayout();
		if(!isset($_POST['categories']))
			$this->view->preview = false;
		else
			$this->view->preview = Lib_Dailybil_Api::preview($_POST['categories']);	
	}

	public function deleteAction()
	{
		$link = $this->mLink->findByAttributes(array('title'=>$_GET['title'],'idUser'=>$this->session->user['id'],'type'=>Model_Link::TYPE_TWITTER));
		$data['connectionObject'] = $link['connectionObject'];
		$data['categories'] = url_decode($_GET['categories']);
		Lib_Dailybil_Api::delete($data);
		$this->_redirect('/dailybil');
	}
}