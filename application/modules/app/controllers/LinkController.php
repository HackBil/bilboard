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
	
	
	public function twitterAction($oauth_token = null, $oauth_verifier = null, $denied = null){
		$oauth_token = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : null;
		$oauth_verifier = isset($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : null;
		
		$twitterApi = new Lib_TwitterApi_TwitterApi();
		$linkModel = new Model_Link();
		$user_id = $this->session->user['id'];

		if(isset($_GET['denied'])){
			//TODO error message '<strong>Error !</strong> Your twitter account has not been linked because you denied the access.'
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
				$similarity = $linkModel->findByAttributes(array('user_id'=>$user_id, 'type'=>Model_Link::TYPE_TWITTER, 'tag'=>$credentials['user_id']));
				
				if(!$similarity){
					//Process response and store token
					$newLink = array();
					$newLink['user_id'] = $user_id; // TODO get current user id
					$newLink['type'] = Model_Link::TYPE_TWITTER;
					$newLink['title'] = $credentials['screen_name'];
					$newLink['connection_object'] = json_encode($credentials);
					$newLink['tag'] = $credentials['user_id'];
					
					if($linkModel->insert($newLink)){
						//TODO '<strong>Awesome !</strong> Your twitter account has been successfully added.'
					}
					else{
						//TODO '<strong>Error !</strong> Your twitter account has not been saved for an unknown reason.'
					}
				}
				else{
					//TODO '<strong>Error !</strong> You already linked this twitter account.'	
				}
			}
			else{
				//TODO '<strong>Error !</strong> We have not been authorized to add your twiter account.'		
			}
		}

		$this->_redirect('/profil');
	}
}