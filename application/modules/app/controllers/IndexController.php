<?php

class IndexController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
		if(isset($_GET['id'])&&isset($_GET['inscription']))
			$this->_redirect('/connexion?id='.$_GET['id'].'&inscription');
		elseif(isset($_GET['id']))
			$this->_redirect('/connexion?id='.$_GET['id']);
		elseif(isset($_GET['url']) && $_GET['url'] != "/")
			$this->_redirect('/connexion?url='.$_GET['url']);
		else
			$this->_redirect('/connexion');			
	}
}