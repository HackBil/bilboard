<?php

class IndexController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
	}

	public function indexAction()
	{
		$this->view->variable = "coucou";
	}
}