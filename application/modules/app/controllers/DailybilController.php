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
			$_POST['token'] = "abcd";
			Lib_Dailybil_Api::create($_POST);
		}
		$this->view->categories = Lib_Dailybil_Api::categories();
		$links = $this->mLink->findAllByAttributes(array('idUser'=>$this->session->user['id'],'type'=>Model_Link::TYPE_TWITTER));
		foreach($links as $link)
		{
			$searches[$link['title']]= Lib_Dailybil_Api::searches("abcd");
		}
		$this->view->searches = $searches;
		$this->view->links = $links;
	}
}