<?php

class LinkController extends Lib_AC_AppController
{	
    public function init()
    {
		parent::init();
	}

	/**
	 * Action that will display the front view
	 */
	public function indexAction()
	{
		$this->view->variable = "Front View to add your social links account";
	}
	
	/**
	 * Action that handles twitter calls and link creation in db
	 */
	public function twitterAction(){
		$oauth_token = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : null;
		$oauth_verifier = isset($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : null;
		
		$twitterApi = new Lib_TwitterApi_TwitterApi();
		$linkModel = new Model_Link();
		$user_id = $this->session->user['id'];

		if(isset($_GET['denied'])){
			$msg = '<strong>Error !</strong> Your twitter account has not been linked because you denied the access.';
			$this->flashMessenger->addMessage(array('message' => $msg, 'status' => 'error'));
		}
		else if(!$oauth_token || !$oauth_verifier){
			$params = array(
				'oauth_callback'=>urlencode(DOMAIN."/link/twitter"),
			);

			$url = $twitterApi->request_token();
			$this->_redirect($url);
		}
		else{
			$credentials = $twitterApi->access_token($oauth_token, $oauth_verifier);
			if($credentials){
				/** $credentials array:
				 'oauth_token' => string '62234258-BnwPWpNqr3xY2fH3QbHmx8jmAMh1ZG6hQEUdTeu66' (length=50)
				 'oauth_token_secret' => string 'hF92M9dqy6ppNJe2GqgOUs8c8QCFnBB5JGb5YRUv4YgqX' (length=45)
				 'user_id' => string '62234258' (length=8)
				 'screen_name' => string 'benomite' (length=8)
				 */
				 
				//check if this link already exists in our dd
				$similarity = $linkModel->findByAttributes(array('idUser'=>$user_id, 'type'=>Model_Link::TYPE_TWITTER, 'tag'=>$credentials['user_id']));
				
				if(!$similarity){
					//Process response and store token
					$newLink = array();
					$newLink['idUser'] = $user_id; // TODO get current user id
					$newLink['type'] = Model_Link::TYPE_TWITTER;
					$newLink['title'] = $credentials['screen_name'];
					$newLink['connectionObject'] = json_encode($credentials);
					$newLink['tag'] = $credentials['user_id'];
					
					$linkModel->insert($newLink);
					
					$msg = '<strong>Awesome !</strong> Your twitter account has been successfully added.';
					$this->flashMessenger->addMessage(array('message' => $msg, 'status' => 'success'));

				}
				else{
					$msg = '<strong>Error !</strong> You already linked this twitter account.';
					$this->flashMessenger->addMessage(array('message' => $msg, 'status' => 'error'));
				}
			}
			else{
				$msg = '<strong>Error !</strong> We have not been authorized to add your twitter account.';	
				$this->flashMessenger->addMessage(array('message' => $msg, 'status' => 'error'));
			}
		}

		$this->_redirect('/profil');
	}

	public function deleteAction(){
		if(isset($_GET['id'])){
			
			$linkModel = new Model_Link();
			$linkModel->deleteById($_GET['id']);
			
			$msg = 'Le lien avec le reseau social a été correctement supprimé.';	
			$this->flashMessenger->addMessage(array('message' => $msg, 'status' => 'success'));
			
		}
		else{
			$msg = 'Vous ne pouvez pas acceder à cette action';	
			$this->flashMessenger->addMessage(array('message' => $msg, 'status' => 'error'));
			
		}
		
		$this->_redirect('/profil');
	}
}