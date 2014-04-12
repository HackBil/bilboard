<?php

class Lib_AC_AppController extends Zend_Controller_Action
{
	public function init()
	{
        if (Zend_Registry::isRegistered('config'))
            $this->config = Zend_Registry::get('config');
        else
            $this->config = null;
                        
        // Gestion de la session
		if(Zend_Registry::isRegistered('session'))
        {
            $this->session = Zend_Registry::get('session');
			if($this->config != null)
				$this->session->setExpirationSeconds($this->config->duree_session*3600);
        }
        if(!isset($this->session->client) && $this->_request->controller != "index")
        {
        	$url = explode("/",$_SERVER['REQUEST_URI']);
			echo "<script>window.top.location='/?url=/". urlencode($url[1])."'</script>";
			exit;
        }
        $this->view->session = $this->session;
        
		//layout
		$this->_helper->layout->setLayout('layout_app');

		if (isset($this->session->client['id'])) {
			
			// Récupération des informations concernant le controller (fil d'ariane, icône,...)
			$model_page = new Model_Page();
			$this->controller = $model_page->controllerByUrl($this->_request->controller);
			$this->view->controller = $this->controller;
			$this->action = $model_page->actionByUrl($this->_request->action);
			$this->view->action = $this->action;
			$this->view->breadcrumb = Lib_Layout::breadcrumb($this->controller, $this->action);
			$this->menuItems = $model_page->menu();
			$this->view->menu = Lib_Layout::menu($this->menuItems,$this->controller);



			// Récupération des messages non-lus
			$mMessagerie = new Model_Messagerie();
			$messages = $mMessagerie->index($this->session->client['id'],$this->session->client['copros'][0]['id']);
			$messagesNonLus = array();
			foreach($messages as $message)
			{
				if($message['lu'] == 0)
					$messagesNonLus[]= $message;
			}
			$this->view->messagesNonLus = $messagesNonLus;

			if($this->controller['url'] != "abonnement")
			{
				if($this->session->client['copros'][0]['paiement'] == 0)
				{
					if($this->session->client['copros'][0]['inscription'] == 0)
					{
						if(time()>mktime(0,0,0,3,1,2014))
							$this->_redirect('/abonnement');
					}
					else
					{
						if(time()>mktime(0,0,0,date("m",$this->session->client['copros'][0]['inscription'])+3,date("d",$this->session->client['copros'][0]['inscription'])+1,date("Y",$this->session->client['copros'][0]['inscription'])))
							$this->_redirect('/abonnement');
					}
				}
				else
				{
					if(mktime(0,0,0,date("m",$this->session->client['copros'][0]['paiement']),date("d",$this->session->client['copros'][0]['paiement']),date("Y",$this->session->client['copros'][0]['paiement'])+1)-time()<0)
						$this->_redirect('/abonnement');
				}
			}

			// Récupération des notifications
			$mActualites = new Model_Actualites();
			array_reverse($this->session->notifications);
			$newnotifs = $mActualites->notifications_index($this->session->client['id'],$this->session->client['copros'][0]['id'],$this->session->client['copros'][0]['profil'],$this->session->client['lastActivity']);
			foreach($newnotifs as $newnotif)
				$this->session->notifications[] = $newnotif;
			array_reverse($this->session->notifications);
			$this->session->nbNotifications += count($newnotifs);

			$mClient = new Model_Client();
			$donnees['lastActivity'] = time();
			$mClient->update($donnees,$this->session->client['id']);
			$this->session->client['lastActivity'] = $donnees['lastActivity'];
		}
	}

	public function ajoutcomAction ()
	{
		$mCommentaires = new Model_Commentaires();

		// On ne charge pas le layout pour une requête AJAX
		$this->_helper->layout->disableLayout();

		// On ne charge même pas la vue.
        $this->_helper->viewRenderer->setNoRender();
        if (isset($_POST['commentaire']) && !empty($_POST['commentaire'])) 
        {
        	$post['idClient'] = $this->session->client['id'];
        	$post['idModule'] = $this->controller['id'];
        	$post['date'] = time();
        	$post['contenu'] = $_POST['commentaire'];
        	$post['idElt'] = $_POST['idElt'];
        	$idCommentaire = $mCommentaires->ajouter($post);
            $commentaire = $mCommentaires->voir($idCommentaire);
        	echo Lib_Commentaires::commentaire($commentaire,$this->session->client);

        	if($this->controller['url'] == "messagerie")
        	{
         	   $this->mMessagerie->updateParticipant($this->session->client['id'],$post['idElt'],false);
        	}
        	if($this->controller['url'] != "messagerie")
        	{
	        	// Ajout dans le flux
		        $_POST['idClient'] = $this->session->client['id'];
		        $_POST['idModule'] = $this->controller['id'];
		        $this->mActualites->add($_POST,'commentaire',$_POST['idElt']);        		
        	}
        }
	}

	public function supprcomAction ()
	{
		$mCommentaires = new Model_Commentaires();
		$mActualites = new Model_Actualites();

		// On ne charge pas le layout pour une requête AJAX
		$this->_helper->layout->disableLayout();

		// On ne charge même pas la vue.
        $this->_helper->viewRenderer->setNoRender();
        if (isset($_POST['id'])) 
        {
            $commentaire = $mCommentaires->voir($_POST['id']);
            $mActualites->deleteAction($commentaire['idModule'],$commentaire['idElt'],$commentaire['idClient'],$commentaire['date'],'commentaire');
        	$mCommentaires->delete($_POST['id']);
        }
	}
}