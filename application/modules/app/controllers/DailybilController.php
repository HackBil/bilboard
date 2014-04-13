<?php

class DailybilController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
	}

	public function indexAction()
	{
		$this->view->categories = Lib_Dailybil_Api::categories();
	}
}